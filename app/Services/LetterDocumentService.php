<?php

namespace App\Services;

use App\Exceptions\BsreException;
use App\Models\AuditLog;
use App\Models\Citizen;
use App\Models\Official;
use App\Models\ServiceRequest;
use App\Models\Setting;
use App\Models\User;
use App\Support\LetterTemplatePresets;
use Barryvdh\DomPDF\Facade\Pdf;
use chillerlan\QRCode\Common\EccLevel;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class LetterDocumentService
{
    public const DISK = 'local';

    public const BASE_DIRECTORY = 'service-requests';

    public const FOOTER_TEXT = 'Dokumen ini ditandatangani secara elektronik menggunakan sertifikat elektronik yang diterbitkan oleh Balai Sertifikasi Elektronik BSSN.';

    public function __construct(
        private readonly LetterNumberService $letterNumberService,
        private readonly BsreService $bsreService,
    ) {}

    public function isGlobalTteEnabled(): bool
    {
        $raw = Setting::where('key', 'tte_enabled')->value('value');

        return filter_var($raw, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Membangun payload awal surat dari data kependudukan dan identitas pemohon.
     *
     * @return array<string, string>
     */
    public function buildDefaultPayload(ServiceRequest $request): array
    {
        $citizen = Citizen::with('dusun')->where('nik', $request->nik)->first();

        return [
            'applicant_name' => (string) ($request->name ?: ($citizen?->name ?? '')),
            'applicant_nik' => (string) $request->nik,
            'applicant_phone' => (string) $request->phone,
            'place_of_birth' => (string) ($citizen?->place_of_birth ?? ''),
            'date_of_birth' => $citizen?->date_of_birth ? $citizen->date_of_birth->format('Y-m-d') : '',
            'gender' => (string) ($citizen?->gender ?? ''),
            'religion' => (string) ($citizen?->religion ?? ''),
            'marital_status' => (string) ($citizen?->marital_status ?? ''),
            'job' => (string) ($citizen?->job ?? ''),
            'address' => (string) ($citizen?->address ?? ''),
            'rt' => (string) ($citizen?->rt ?? ''),
            'rw' => (string) ($citizen?->rw ?? ''),
            'dusun' => (string) ($citizen?->dusun?->name ?? ''),
        ];
    }

    /**
     * Menyinkronkan variabel kependudukan ke letter_payload tanpa menimpa data manual saat NIK tidak ditemukan.
     */
    public function syncCitizenPayload(ServiceRequest $request): bool
    {
        $existingPayload = is_array($request->letter_payload) ? $request->letter_payload : [];
        $citizenExists = Citizen::where('nik', $request->nik)->exists();

        if ($citizenExists) {
            $citizenPayload = $this->buildDefaultPayload($request);
            $request->update([
                'letter_payload' => array_merge($existingPayload, $citizenPayload),
            ]);

            return true;
        }

        if ($existingPayload === []) {
            $request->update([
                'letter_payload' => $this->buildDefaultPayload($request),
            ]);
        }

        return false;
    }

    /**
     * Merender HTML lengkap surat resmi siap cetak PDF.
     */
    public function renderHtml(
        ServiceRequest $request,
        bool $includeTteVisual = false,
        ?string $verificationTokenOverride = null,
        ?string $letterNumberOverride = null
    ): string {
        $request->loadMissing('service');
        $settings = Setting::pluck('value', 'key')->toArray();
        $letterPayload = is_array($request->letter_payload) ? $request->letter_payload : [];
        $payload = array_merge(
            $this->formatDateOverrides($request, $this->buildDefaultPayload($request)),
            $this->formatFormDataForRender($request),
            $this->formatDateOverrides($request, $letterPayload)
        );

        if (filled($request->name)) {
            $payload['applicant_name'] = (string) $request->name;
        }
        if (filled($request->nik)) {
            $payload['applicant_nik'] = (string) $request->nik;
        }
        if (filled($request->phone)) {
            $payload['applicant_phone'] = (string) $request->phone;
        }

        $letterDate = $request->letter_date ? Carbon::parse($request->letter_date) : now();
        $signerName = $request->signer_name ?: ($this->resolveDefaultSigner()?->name ?? 'Kepala Desa');
        $signerPosition = $request->signer_position ?: ($this->resolveDefaultSigner()?->position ?? 'Kepala Desa');

        $villageName = $this->stripAdminPrefix((string) ($settings['village_name'] ?? 'Desa'), ['Desa', 'Kelurahan']);
        if ($villageName === '') {
            $villageName = 'Desa';
        }
        $districtName = $this->stripAdminPrefix((string) ($settings['district_name'] ?? ''), ['Kecamatan', 'Kec\.']);
        $regencyName = $this->stripAdminPrefix((string) ($settings['regency_name'] ?? ''), ['Kabupaten', 'Kab\.', 'Kota']);

        $resolvedLetterNumber = $letterNumberOverride ?: ($request->letter_number ?: '-');

        $variables = array_merge($payload, [
            'birth_place_date' => $this->formatBirthPlaceDate($payload),
            'full_address' => $this->formatFullAddress($payload, $villageName),
            'letter_number' => $resolvedLetterNumber,
            'letter_date' => $letterDate->translatedFormat('d F Y'),
            'village_name' => $villageName,
            'district_name' => $districtName,
            'regency_name' => $regencyName,
            'signer_name' => (string) $signerName,
            'signer_position' => (string) $signerPosition,
        ]);

        $template = (string) ($request->service?->letter_template ?? '');
        if (trim($template) === '') {
            $template = LetterTemplatePresets::get('pengantar')['letter_template'];
        }

        $bodyHtml = preg_replace_callback('/\{\{\s*([a-zA-Z0-9_]+)\s*\}\}/', function (array $matches) use ($variables): string {
            $key = $matches[1];
            $value = trim((string) ($variables[$key] ?? ''));

            return e($value !== '' ? $value : '-');
        }, $template) ?? $template;

        $logoDataUri = $this->resolveVillageLogoDataUri($settings['site_logo'] ?? null);
        $qrCodeDataUri = null;
        $verificationUrl = null;
        if ($includeTteVisual) {
            $verificationToken = $verificationTokenOverride ?: ($request->verification_token ?: Str::random(40));
            $verificationUrl = route('letters.verify', $verificationToken);
            $qrCodeDataUri = $this->generateQrCodeDataUri($verificationUrl);
        }

        return view('pdf.service-letter', [
            'request' => $request,
            'settings' => $settings,
            'villageName' => $villageName,
            'districtName' => $districtName,
            'regencyName' => $regencyName,
            'letterTitle' => $request->service?->letter_title ?: strtoupper((string) ($request->service?->title ?? 'SURAT KETERANGAN')),
            'letterNumber' => $resolvedLetterNumber,
            'letterDateFormatted' => $letterDate->translatedFormat('d F Y'),
            'bodyHtml' => $bodyHtml,
            'signerName' => $signerName,
            'signerPosition' => $signerPosition,
            'footerText' => self::FOOTER_TEXT,
            'websiteUrl' => $this->resolveWebsiteDomain(),
            'logoDataUri' => $logoDataUri,
            'qrCodeDataUri' => $qrCodeDataUri,
            'verificationUrl' => $verificationUrl,
            'showTteVisual' => $includeTteVisual,
            'isGlobalTteEnabled' => $this->isGlobalTteEnabled(),
        ])->render();
    }

    /**
     * Membuat draf PDF surat di penyimpanan privat.
     */
    public function generateDraftPdf(
        ServiceRequest $request,
        ?User $actor = null,
        bool $allocateLetterNumber = false
    ): string {
        $request->loadMissing('service');

        if (! $request->service) {
            throw new \RuntimeException('Layanan permohonan tidak ditemukan.');
        }

        if (empty($request->letter_payload)) {
            $request->letter_payload = $this->buildDefaultPayload($request);
        } elseif (is_array($request->letter_payload)) {
            $payload = $request->letter_payload;
            if (filled($request->name)) {
                $payload['applicant_name'] = (string) $request->name;
            }
            if (filled($request->nik)) {
                $payload['applicant_nik'] = (string) $request->nik;
            }
            if (filled($request->phone)) {
                $payload['applicant_phone'] = (string) $request->phone;
            }
            $request->letter_payload = $payload;
        }

        if (! $request->letter_date) {
            $request->letter_date = now()->toDateString();
        }

        $previewLetterNumber = null;
        if (empty($request->letter_number)) {
            $targetDate = Carbon::parse($request->letter_date);
            if ($allocateLetterNumber || ! $this->isGlobalTteEnabled()) {
                $request->letter_number = $this->letterNumberService->nextNumber($request->service, $targetDate);
            } else {
                $previewLetterNumber = $this->letterNumberService->previewNextNumber($request->service, $targetDate);
            }
        }

        if (empty($request->verification_token)) {
            $request->verification_token = Str::random(40);
        }

        if (empty($request->signer_name) || empty($request->signer_position)) {
            $defaultSigner = $this->resolveDefaultSigner();
            if ($defaultSigner) {
                $request->signer_official_id = $defaultSigner->id;
                $request->signer_name = $defaultSigner->name;
                $request->signer_position = $defaultSigner->position;
            }
        }

        $html = $this->renderHtml($request, false, null, $previewLetterNumber);
        $pdfBinary = Pdf::loadHTML($html)->setPaper('a4', 'portrait')->output();

        $relativePath = self::BASE_DIRECTORY.'/'.$request->id.'/draft.pdf';
        Storage::disk(self::DISK)->put($relativePath, $pdfBinary);

        $request->draft_pdf_path = $relativePath;
        if (! $request->isTteSigned() && ! $request->isTteRevoked()) {
            $request->tte_status = ServiceRequest::TTE_DRAFT;
        }
        $request->save();

        $this->recordAudit($request, 'tte_draft_generated', 'Draf PDF surat dibuat oleh petugas.', $actor, [
            'letter_number' => $request->letter_number ?: $previewLetterNumber,
        ]);

        return $relativePath;
    }

    /**
     * Menandatangani surat melalui BSrE dan menyimpan PDF resmi.
     */
    public function signWithBsre(
        ServiceRequest $request,
        Official $signer,
        string $signerNik,
        string $passphrase,
        ?User $actor = null
    ): ServiceRequest {
        $current = $request->fresh() ?? $request;

        if ($current->isTteSigned()) {
            throw new BsreException('Surat ini sudah ditandatangani secara elektronik. Cabut surat terlebih dahulu untuk menandatangani ulang.');
        }

        if ($current->status === 'Ditolak') {
            throw new BsreException('Permohonan berstatus ditolak tidak dapat ditandatangani secara elektronik.');
        }

        $wasRevoked = $current->isTteRevoked();

        $this->generateDraftPdf($current, $actor, true);

        $freshRequest = $current->fresh();
        $targetToken = $wasRevoked
            ? Str::random(40)
            : ($freshRequest->verification_token ?: Str::random(40));

        $renderTarget = $freshRequest->replicate();
        $renderTarget->id = $freshRequest->id;
        $renderTarget->signer_name = $signer->name;
        $renderTarget->signer_position = $signer->position;

        $toSignRelativePath = self::BASE_DIRECTORY.'/'.$current->id.'/to-sign.pdf';
        $toSignPdfBinary = Pdf::loadHTML($this->renderHtml($renderTarget, true, $targetToken))
            ->setPaper('a4', 'portrait')
            ->output();
        Storage::disk(self::DISK)->put($toSignRelativePath, $toSignPdfBinary);
        $toSignAbsolutePath = Storage::disk(self::DISK)->path($toSignRelativePath);

        try {
            $result = $this->bsreService->signPdf($toSignAbsolutePath, $signerNik, $passphrase);
        } catch (Throwable $e) {
            $this->recordAudit($freshRequest, 'tte_sign_failed', 'Penandatanganan elektronik melalui BSrE gagal diproses.', $actor, [
                'letter_number' => $freshRequest->letter_number,
                'signer_name' => $signer->name,
                'error' => mb_substr($e->getMessage(), 0, 250),
            ]);

            throw $e;
        } finally {
            Storage::disk(self::DISK)->delete($toSignRelativePath);
        }

        $signedRelativePath = self::BASE_DIRECTORY.'/'.$request->id.'/signed.pdf';
        Storage::disk(self::DISK)->put($signedRelativePath, $result['pdf_binary']);

        $cleanNik = preg_replace('/\D/', '', $signerNik);
        $maskedSignerNik = strlen($cleanNik) >= 16
            ? substr($cleanNik, 0, 6).'******'.substr($cleanNik, -4)
            : substr($cleanNik, 0, 4).'***';

        $request->forceFill([
            'verification_token' => $targetToken,
            'signer_official_id' => $signer->id,
            'signer_name' => $signer->name,
            'signer_position' => $signer->position,
            'signed_pdf_path' => $signedRelativePath,
            'signed_pdf_hash' => hash('sha256', $result['pdf_binary']),
            'tte_status' => ServiceRequest::TTE_SIGNED,
            'status' => 'Selesai',
            'signer_nik_masked' => $maskedSignerNik,
            'signed_at' => now(),
            'signed_by_user_id' => $actor?->id,
            'revoked_at' => null,
            'revoked_by_user_id' => null,
            'revocation_reason' => null,
        ])->save();

        $this->recordAudit($request, 'tte_signed', 'Surat layanan ditandatangani secara elektronik melalui BSrE.', $actor, [
            'letter_number' => $request->letter_number,
            'signer_name' => $signer->name,
            'signer_position' => $signer->position,
            'signed_pdf_hash' => $request->signed_pdf_hash,
            'is_mock' => $result['is_mock'],
        ]);

        return $request->fresh();
    }

    /**
     * Mencabut keabsahan surat TTE yang telah diterbitkan.
     */
    public function revoke(ServiceRequest $request, string $reason, ?User $actor = null): ServiceRequest
    {
        if (! $request->isTteSigned()) {
            throw new \RuntimeException('Hanya surat yang sudah ditandatangani secara elektronik yang dapat dicabut.');
        }

        $request->forceFill([
            'tte_status' => ServiceRequest::TTE_REVOKED,
            'revoked_at' => now(),
            'revoked_by_user_id' => $actor?->id,
            'revocation_reason' => trim($reason),
        ])->save();

        $this->recordAudit($request, 'tte_revoked', 'Surat TTE dicabut oleh petugas.', $actor, [
            'letter_number' => $request->letter_number,
            'revocation_reason' => trim($reason),
        ]);

        return $request->fresh();
    }

    /**
     * Mengambil path absolut berkas PDF privat dengan validasi anti path traversal.
     */
    public function resolveSafePdfPath(ServiceRequest $request, string $type = 'signed'): ?string
    {
        $rawPath = $type === 'draft' ? $request->draft_pdf_path : $request->signed_pdf_path;
        if (! $rawPath) {
            return null;
        }

        $normalized = ltrim(str_replace('\\', '/', $rawPath), '/');
        if (str_contains($normalized, '..') || ! str_starts_with($normalized, self::BASE_DIRECTORY.'/'.$request->id.'/')) {
            return null;
        }

        $disk = Storage::disk(self::DISK);
        if (! $disk->exists($normalized)) {
            return null;
        }

        return $disk->path($normalized);
    }

    /**
     * Membentuk nama berkas unduhan PDF yang rapi dan deskriptif.
     */
    public function buildDownloadFilename(ServiceRequest $request, string $type = 'signed'): string
    {
        $request->loadMissing('service');

        $serviceSlug = Str::limit(Str::slug((string) ($request->service?->title ?? 'surat')), 60, '') ?: 'surat';
        $nameSlug = Str::limit(Str::slug((string) ($request->name ?? 'pemohon')), 40, '') ?: 'pemohon';
        $dateCode = ($request->letter_date ?? $request->signed_at ?? now())->format('Ymd');

        if ($type === 'draft') {
            return "draf-{$serviceSlug}-{$nameSlug}-{$dateCode}.pdf";
        }

        return "{$serviceSlug}-{$nameSlug}-{$dateCode}-tte.pdf";
    }

    /**
     * Memformat isian tambahan warga sebelum dirender ke HTML surat (termasuk tanggal Indonesia).
     *
     * @return array<string, string>
     */
    private function formatFormDataForRender(ServiceRequest $request): array
    {
        $formData = is_array($request->form_data) ? $request->form_data : [];

        return $this->formatDateOverrides($request, $formData);
    }

    /**
     * Memformat nilai bertipe tanggal pada payload surat menjadi format tanggal Indonesia.
     *
     * @param  array<string, mixed>  $rawData
     * @return array<string, string>
     */
    private function formatDateOverrides(ServiceRequest $request, array $rawData): array
    {
        if ($rawData === []) {
            return [];
        }

        $dateFieldKeys = [
            'date_of_birth' => true,
        ];
        $formFields = $request->service?->form_fields;
        if (is_array($formFields)) {
            foreach ($formFields as $field) {
                $key = $field['key'] ?? null;
                if (is_string($key) && $key !== '' && ($field['type'] ?? 'text') === 'date') {
                    $dateFieldKeys[$key] = true;
                }
            }
        }

        $formatted = [];
        foreach ($rawData as $key => $value) {
            if (! is_string($key) || $key === '') {
                continue;
            }

            if (! is_scalar($value)) {
                $formatted[$key] = '';

                continue;
            }

            $stringValue = trim((string) $value);
            if ($stringValue !== '' && isset($dateFieldKeys[$key])) {
                try {
                    $formatted[$key] = Carbon::parse($stringValue)->translatedFormat('d F Y');

                    continue;
                } catch (Throwable) {
                    // Fallback ke teks asli jika format tanggal tidak dapat diparsing
                }
            }

            $formatted[$key] = (string) $value;
        }

        return $formatted;
    }

    /**
     * Menghapus awalan administratif wilayah agar tidak terjadi duplikasi pada kop maupun isi surat.
     *
     * @param  array<int, string>  $prefixes
     */
    private function stripAdminPrefix(string $value, array $prefixes): string
    {
        $trimmed = trim($value);
        if ($trimmed === '') {
            return '';
        }

        $pattern = '/^(?:'.implode('|', $prefixes).')\s+/iu';
        $cleaned = preg_replace($pattern, '', $trimmed);

        return trim($cleaned ?? $trimmed);
    }

    /**
     * Menggabungkan tempat dan tanggal lahir tanpa meninggalkan tanda baca kosong.
     *
     * @param  array<string, string>  $payload
     */
    private function formatBirthPlaceDate(array $payload): string
    {
        $place = trim((string) ($payload['place_of_birth'] ?? ''));
        $date = trim((string) ($payload['date_of_birth'] ?? ''));

        $parts = array_values(array_filter([$place, $date], fn (string $part): bool => $part !== ''));

        return $parts !== [] ? implode(', ', $parts) : '-';
    }

    /**
     * Merangkai alamat lengkap pemohon hanya dari komponen yang terisi.
     *
     * @param  array<string, string>  $payload
     */
    private function formatFullAddress(array $payload, string $villageName): string
    {
        $address = trim((string) ($payload['address'] ?? ''));
        $rt = trim((string) ($payload['rt'] ?? ''));
        $rw = trim((string) ($payload['rw'] ?? ''));
        $dusun = trim((string) ($payload['dusun'] ?? ''));

        $rtRwParts = [];
        if ($rt !== '') {
            $rtRwParts[] = "RT {$rt}";
        }
        if ($rw !== '') {
            $rtRwParts[] = "RW {$rw}";
        }
        $rtRw = implode(' ', $rtRwParts);

        $streetLine = trim(implode(' ', array_filter([$address, $rtRw], fn (string $part): bool => $part !== '')));

        $segments = [];
        if ($streetLine !== '') {
            $segments[] = $streetLine;
        }
        if ($dusun !== '') {
            $segments[] = "Dusun {$dusun}";
        }
        if ($villageName !== '') {
            $segments[] = "Desa {$villageName}";
        }

        return $segments !== [] ? implode(', ', $segments) : '-';
    }

    private function resolveWebsiteDomain(): string
    {
        $rawUrl = (string) (url('/') ?: config('app.url'));
        $host = parse_url($rawUrl, PHP_URL_HOST);

        if (! is_string($host) || trim($host) === '') {
            $host = parse_url((string) config('app.url'), PHP_URL_HOST) ?: '';
        }

        return (string) preg_replace('/^www\./i', '', trim((string) $host));
    }

    private function resolveDefaultSigner(): ?Official
    {
        $defaultOfficialId = Setting::where('key', 'tte_default_signer_official_id')->value('value');
        if ($defaultOfficialId) {
            $official = Official::find($defaultOfficialId);
            if ($official) {
                return $official;
            }
        }

        return Official::whereNull('parent_id')->orderBy('order')->first()
            ?? Official::orderBy('order')->first();
    }

    /**
     * Cache data URI logo per path selama proses berjalan untuk menghemat pembacaan berkas dan memori.
     *
     * @var array<string, string>
     */
    private static array $logoDataUriCache = [];

    private function resolveVillageLogoDataUri(?string $logoPath): ?string
    {
        $candidates = [
            public_path('img/sinjai.png'),
            public_path('img/sinjai.webp'),
        ];

        if ($logoPath) {
            $normalized = ltrim(str_replace('\\', '/', $logoPath), '/');
            if (! str_contains($normalized, '..')) {
                $candidates[] = storage_path('app/public/'.$normalized);
                $candidates[] = public_path('storage/'.$normalized);
            }
        }

        foreach ($candidates as $path) {
            if (isset(self::$logoDataUriCache[$path])) {
                return self::$logoDataUriCache[$path];
            }

            if (is_file($path) && is_readable($path)) {
                $mime = mime_content_type($path) ?: 'image/png';
                if (($mime === 'image/webp' || str_ends_with(strtolower($path), '.webp')) && function_exists('imagecreatefromwebp') && function_exists('imagepng')) {
                    $img = @imagecreatefromwebp($path);
                    if ($img !== false) {
                        ob_start();
                        imagealphablending($img, false);
                        imagesavealpha($img, true);
                        imagepng($img);
                        $pngBinary = ob_get_clean();
                        imagedestroy($img);

                        if (is_string($pngBinary) && $pngBinary !== '') {
                            return self::$logoDataUriCache[$path] = 'data:image/png;base64,'.base64_encode($pngBinary);
                        }
                    }
                }

                $content = file_get_contents($path);
                if ($content !== false) {
                    return self::$logoDataUriCache[$path] = 'data:'.$mime.';base64,'.base64_encode($content);
                }
            }
        }

        return null;
    }

    private function generateQrCodeDataUri(string $url): ?string
    {
        try {
            $options = new QROptions([
                'outputType' => QRCode::OUTPUT_IMAGE_PNG,
                'eccLevel' => EccLevel::M,
                'scale' => 5,
                'quietzoneSize' => 2,
                'imageBase64' => true,
            ]);

            return (new QRCode($options))->render($url);
        } catch (Throwable) {
            return null;
        }
    }

    /**
     * @param  array<string, mixed>  $newValues
     */
    private function recordAudit(
        ServiceRequest $request,
        string $event,
        string $description,
        ?User $actor,
        array $newValues = []
    ): void {
        AuditLog::create([
            'user_id' => $actor?->id,
            'user_name' => $actor?->name ?? 'Sistem',
            'event' => $event,
            'auditable_type' => ServiceRequest::class,
            'auditable_id' => $request->id,
            'description' => $description,
            'new_values' => $newValues,
            'ip_address' => request()?->ip(),
            'user_agent' => request()?->userAgent(),
        ]);
    }
}
