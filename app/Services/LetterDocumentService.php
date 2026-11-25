<?php

namespace App\Services;

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
     * Membangun payload awal surat dari data kependudukan dan isian warga.
     *
     * @return array<string, string>
     */
    public function buildDefaultPayload(ServiceRequest $request): array
    {
        $citizen = Citizen::with('dusun')->where('nik', $request->nik)->first();

        $payload = [
            'applicant_name' => (string) ($citizen?->name ?: $request->name),
            'applicant_nik' => (string) $request->nik,
            'applicant_phone' => (string) $request->phone,
            'place_of_birth' => (string) ($citizen?->place_of_birth ?? ''),
            'date_of_birth' => $citizen?->date_of_birth ? $citizen->date_of_birth->translatedFormat('d F Y') : '',
            'gender' => (string) ($citizen?->gender ?? ''),
            'religion' => (string) ($citizen?->religion ?? ''),
            'marital_status' => (string) ($citizen?->marital_status ?? ''),
            'job' => (string) ($citizen?->job ?? ''),
            'address' => (string) ($citizen?->address ?? ''),
            'rt' => (string) ($citizen?->rt ?? ''),
            'rw' => (string) ($citizen?->rw ?? ''),
            'dusun' => (string) ($citizen?->dusun?->name ?? ''),
        ];

        $formData = is_array($request->form_data) ? $request->form_data : [];
        foreach ($formData as $key => $value) {
            if (is_string($key) && $key !== '') {
                $payload[$key] = is_scalar($value) ? (string) $value : '';
            }
        }

        return $payload;
    }

    /**
     * Merender HTML lengkap surat resmi siap cetak PDF.
     */
    public function renderHtml(ServiceRequest $request, bool $includeTteVisual = false): string
    {
        $request->loadMissing('service');
        $settings = Setting::pluck('value', 'key')->toArray();
        $payload = array_merge(
            $this->buildDefaultPayload($request),
            is_array($request->letter_payload) ? $request->letter_payload : [],
            is_array($request->form_data) ? $request->form_data : []
        );

        $letterDate = $request->letter_date ? Carbon::parse($request->letter_date) : now();
        $signerName = $request->signer_name ?: ($this->resolveDefaultSigner()?->name ?? 'Kepala Desa');
        $signerPosition = $request->signer_position ?: ($this->resolveDefaultSigner()?->position ?? 'Kepala Desa');

        $variables = array_merge($payload, [
            'letter_number' => $request->letter_number ?: '-',
            'letter_date' => $letterDate->translatedFormat('d F Y'),
            'village_name' => (string) ($settings['village_name'] ?? 'Desa'),
            'district_name' => (string) ($settings['district_name'] ?? ''),
            'regency_name' => (string) ($settings['regency_name'] ?? ''),
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
        if ($includeTteVisual) {
            $verificationToken = $request->verification_token ?: Str::random(40);
            $qrCodeDataUri = $this->generateQrCodeDataUri(route('letters.verify', $verificationToken));
        }

        return view('pdf.service-letter', [
            'request' => $request,
            'settings' => $settings,
            'letterTitle' => $request->service?->letter_title ?: strtoupper((string) ($request->service?->title ?? 'SURAT KETERANGAN')),
            'letterNumber' => $request->letter_number ?: '-',
            'letterDateFormatted' => $letterDate->translatedFormat('d F Y'),
            'bodyHtml' => $bodyHtml,
            'signerName' => $signerName,
            'signerPosition' => $signerPosition,
            'footerText' => self::FOOTER_TEXT,
            'websiteUrl' => rtrim((string) url('/'), '/'),
            'logoDataUri' => $logoDataUri,
            'qrCodeDataUri' => $qrCodeDataUri,
            'showTteVisual' => $includeTteVisual,
        ])->render();
    }

    /**
     * Membuat draf PDF surat di penyimpanan privat.
     */
    public function generateDraftPdf(ServiceRequest $request, ?User $actor = null): string
    {
        $request->loadMissing('service');

        if (! $request->service) {
            throw new \RuntimeException('Layanan permohonan tidak ditemukan.');
        }

        if (empty($request->letter_payload)) {
            $request->letter_payload = $this->buildDefaultPayload($request);
        }

        if (! $request->letter_date) {
            $request->letter_date = now()->toDateString();
        }

        if (empty($request->letter_number)) {
            $request->letter_number = $this->letterNumberService->nextNumber(
                $request->service,
                Carbon::parse($request->letter_date)
            );
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

        $html = $this->renderHtml($request);
        $pdfBinary = Pdf::loadHTML($html)->setPaper('a4', 'portrait')->output();

        $relativePath = self::BASE_DIRECTORY.'/'.$request->id.'/draft.pdf';
        Storage::disk(self::DISK)->put($relativePath, $pdfBinary);

        $request->draft_pdf_path = $relativePath;
        if (! $request->isTteSigned()) {
            $request->tte_status = ServiceRequest::TTE_DRAFT;
        }
        $request->save();

        $this->recordAudit($request, 'tte_draft_generated', 'Draf PDF surat dibuat oleh petugas.', $actor, [
            'letter_number' => $request->letter_number,
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
        if (empty($request->verification_token)) {
            $request->verification_token = Str::random(40);
        }

        $request->signer_official_id = $signer->id;
        $request->signer_name = $signer->name;
        $request->signer_position = $signer->position;
        $request->save();

        $this->generateDraftPdf($request, $actor);

        $toSignRelativePath = self::BASE_DIRECTORY.'/'.$request->id.'/to-sign.pdf';
        $toSignPdfBinary = Pdf::loadHTML($this->renderHtml($request->fresh(), true))
            ->setPaper('a4', 'portrait')
            ->output();
        Storage::disk(self::DISK)->put($toSignRelativePath, $toSignPdfBinary);
        $toSignAbsolutePath = Storage::disk(self::DISK)->path($toSignRelativePath);

        $verificationUrl = route('letters.verify', $request->verification_token);

        try {
            $result = $this->bsreService->signPdf($toSignAbsolutePath, $signerNik, $passphrase, $verificationUrl);
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

    private function resolveVillageLogoDataUri(?string $logoPath): ?string
    {
        if (! $logoPath) {
            return null;
        }

        $normalized = ltrim(str_replace('\\', '/', $logoPath), '/');
        if (str_contains($normalized, '..')) {
            return null;
        }

        $candidates = [
            storage_path('app/public/'.$normalized),
            public_path('storage/'.$normalized),
        ];

        foreach ($candidates as $path) {
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
                            return 'data:image/png;base64,'.base64_encode($pngBinary);
                        }
                    }
                }

                $content = file_get_contents($path);
                if ($content !== false) {
                    return 'data:'.$mime.';base64,'.base64_encode($content);
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
        } catch (\Throwable) {
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
