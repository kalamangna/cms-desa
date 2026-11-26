<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\ServiceRequest;
use App\Services\LetterDocumentService;
use App\Services\ServiceAttachmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Throwable;

class ServiceRequestController extends Controller
{
    public function __construct(
        private readonly ServiceAttachmentService $attachmentService,
        private readonly LetterDocumentService $letterDocumentService
    ) {}

    public function store(Request $request): RedirectResponse
    {
        $service = $request->filled('service_id')
            ? Service::with('requirementItems')->active()->find($request->input('service_id'))
            : null;

        $rules = [
            'nik' => ['required', 'numeric', 'digits:16'],
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'regex:/^[0-9+\-\s()]{8,25}$/'],
            'service_id' => ['required', Rule::exists('services', 'id')->where('is_active', true)],
            'attachments' => ['nullable', 'array'],
            'form_data' => ['nullable', 'array'],
        ];

        $messages = [
            'nik.required' => 'NIK pemohon wajib diisi.',
            'nik.digits' => 'NIK harus tepat 16 digit angka.',
            'nik.numeric' => 'NIK hanya boleh berisi angka.',
            'name.required' => 'Nama lengkap wajib diisi.',
            'phone.required' => 'Nomor WhatsApp wajib diisi.',
            'phone.regex' => 'Nomor WhatsApp tidak valid. Minimal 8 digit angka.',
            'service_id.required' => 'Jenis layanan wajib dipilih.',
            'service_id.exists' => 'Jenis layanan tidak ditemukan.',
        ];

        $mimes = implode(',', ServiceAttachmentService::ALLOWED_EXTENSIONS);
        $mimeTypes = implode(',', ServiceAttachmentService::ALLOWED_MIME_TYPES);

        if ($service) {
            foreach ($service->requirementItems as $requirement) {
                $field = 'attachments.'.$requirement->id;
                $rules[$field] = [
                    $requirement->is_required ? 'required' : 'nullable',
                    'file',
                    'mimes:'.$mimes,
                    'mimetypes:'.$mimeTypes,
                    'max:'.ServiceAttachmentService::MAX_FILE_SIZE_KB,
                ];

                $messages[$field.'.required'] = "Berkas {$requirement->name} wajib diunggah.";
                $messages[$field.'.file'] = "Berkas {$requirement->name} gagal diunggah.";
                $messages[$field.'.mimes'] = "Berkas {$requirement->name} harus berformat JPG, PNG, WebP, atau PDF.";
                $messages[$field.'.mimetypes'] = "Berkas {$requirement->name} harus berformat JPG, PNG, WebP, atau PDF.";
                $messages[$field.'.max'] = "Berkas {$requirement->name} gagal diunggah. Maksimal 2 MB.";
            }

            if ($service->is_tte_enabled && is_array($service->form_fields)) {
                foreach ($service->form_fields as $fieldDef) {
                    $key = $fieldDef['key'] ?? null;
                    $label = $fieldDef['label'] ?? $key;
                    if (! is_string($key) || $key === '') {
                        continue;
                    }

                    $inputKey = 'form_data.'.$key;
                    $isRequired = (bool) ($fieldDef['is_required'] ?? false);
                    $type = $fieldDef['type'] ?? 'text';

                    $fieldRules = [$isRequired ? 'required' : 'nullable'];
                    if ($type === 'date') {
                        $fieldRules[] = 'date';
                    } elseif ($type === 'number') {
                        $fieldRules[] = 'numeric';
                    } else {
                        $fieldRules[] = 'string';
                        $fieldRules[] = 'max:1000';
                    }

                    $rules[$inputKey] = $fieldRules;
                    $messages[$inputKey.'.required'] = "{$label} wajib diisi.";
                    $messages[$inputKey.'.date'] = "{$label} harus berupa tanggal yang valid.";
                    $messages[$inputKey.'.numeric'] = "{$label} harus berupa angka.";
                    $messages[$inputKey.'.max'] = "{$label} maksimal 1000 karakter.";
                }
            }
        }

        $validated = $request->validate($rules, $messages);

        $clean = fn (?string $v) => $v !== null ? trim(strip_tags(preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $v))) : null;

        $sanitizedFormData = [];
        if ($service && $service->is_tte_enabled && is_array($service->form_fields)) {
            $rawFormData = $validated['form_data'] ?? [];
            foreach ($service->form_fields as $fieldDef) {
                $key = $fieldDef['key'] ?? null;
                if (is_string($key) && $key !== '' && array_key_exists($key, $rawFormData)) {
                    $val = $rawFormData[$key];
                    $sanitizedFormData[$key] = is_scalar($val) ? $clean((string) $val) : null;
                }
            }
        }

        $sanitized = [
            'nik' => trim($validated['nik']),
            'name' => $clean($validated['name']),
            'phone' => preg_replace('/[^0-9+]/', '', $validated['phone']),
            'service_id' => $validated['service_id'],
            'form_data' => $sanitizedFormData !== [] ? $sanitizedFormData : null,
        ];

        $createdRequestId = null;

        try {
            $serviceRequest = DB::transaction(function () use ($request, $service, $sanitized, &$createdRequestId): ServiceRequest {
                $serviceRequest = ServiceRequest::create($sanitized);
                $createdRequestId = $serviceRequest->id;

                if ($service) {
                    foreach ($service->requirementItems as $requirement) {
                        $uploadedFile = $request->file('attachments.'.$requirement->id);
                        if ($uploadedFile !== null) {
                            $this->attachmentService->storeUploadedFile($uploadedFile, $serviceRequest, $requirement);
                        }
                    }
                }

                return $serviceRequest;
            });
        } catch (Throwable $e) {
            if ($createdRequestId !== null) {
                $this->attachmentService->deleteRequestDirectory($createdRequestId);
            }

            throw $e;
        }

        return redirect()->route('layanan')
            ->with('success', 'Pengajuan layanan berhasil dikirim')
            ->with('ticket_number', $serviceRequest->ticket_number);
    }

    public function track(Request $request): JsonResponse|View
    {
        $request->validate([
            'ticket_number' => ['required', 'string', 'max:30'],
        ]);

        $serviceRequest = ServiceRequest::with(['service', 'attachments'])
            ->where('ticket_number', trim($request->ticket_number))
            ->first();

        if ($request->wantsJson() || $request->ajax()) {
            if (! $serviceRequest) {
                return response()->json(['found' => false, 'message' => 'Nomor tiket tidak ditemukan.'], 404);
            }

            return response()->json($this->buildTrackPayload($serviceRequest));
        }

        $services = Cache::remember('services_list', 3600, function () {
            return Service::with('requirementItems')->active()->ordered()->get();
        });

        return view('pages.layanan', compact('services', 'serviceRequest'))
            ->with('searched_ticket', $request->ticket_number);
    }

    public function downloadSignedLetter(Request $request): BinaryFileResponse|RedirectResponse
    {
        if (! $request->filled('download_nik') && $request->filled('nik')) {
            $request->merge(['download_nik' => $request->input('nik')]);
        }

        $validator = Validator::make($request->all(), [
            'ticket_number' => ['required', 'string', 'max:30'],
            'download_nik' => ['required', 'numeric', 'digits:16'],
        ], [
            'ticket_number.required' => 'Nomor tiket wajib diisi.',
            'download_nik.required' => 'NIK pemohon wajib diisi.',
            'download_nik.digits' => 'NIK harus tepat 16 digit angka.',
            'download_nik.numeric' => 'NIK hanya boleh berisi angka.',
        ]);

        if ($validator->fails()) {
            $ticket = trim((string) $request->input('ticket_number', ''));

            return redirect()->route('service-requests.track', $ticket !== '' ? ['ticket_number' => $ticket] : [])
                ->withErrors($validator)
                ->withInput();
        }

        $validated = $validator->validated();

        $serviceRequest = ServiceRequest::where('ticket_number', trim($validated['ticket_number']))->first();

        if (! $serviceRequest || ! hash_equals((string) $serviceRequest->nik, trim((string) $validated['download_nik']))) {
            return redirect()->route('service-requests.track', ['ticket_number' => $validated['ticket_number']])
                ->withErrors(['download_nik' => 'NIK tidak sesuai dengan nomor tiket permohonan.']);
        }

        if ($serviceRequest->tte_status !== ServiceRequest::TTE_SIGNED) {
            return redirect()->route('service-requests.track', ['ticket_number' => $validated['ticket_number']])
                ->withErrors(['download_nik' => 'Surat elektronik belum tersedia atau telah dicabut.']);
        }

        $fullPath = $this->letterDocumentService->resolveSafePdfPath($serviceRequest, 'signed');
        abort_unless($fullPath !== null, 404);

        $filename = $this->letterDocumentService->buildDownloadFilename($serviceRequest, 'signed');

        return response()->download($fullPath, $filename, [
            'Cache-Control' => 'no-store, private',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function buildTrackPayload(ServiceRequest $serviceRequest): array
    {
        $canDownload = $serviceRequest->isTteSigned();

        return [
            'found' => true,
            'ticket_number' => $serviceRequest->ticket_number,
            'name' => $serviceRequest->name,
            'nik_masked' => substr($serviceRequest->nik, 0, 4).'**********',
            'service_title' => $serviceRequest->service?->title ?? 'Layanan Umum',
            'status' => $serviceRequest->status,
            'tte_status' => $serviceRequest->tte_status ?? ServiceRequest::TTE_NONE,
            'letter_number' => $serviceRequest->letter_number,
            'signed_at' => $serviceRequest->signed_at?->translatedFormat('d M Y, H:i'),
            'can_download_letter' => $canDownload,
            'verification_url' => $serviceRequest->verification_token && ($canDownload || $serviceRequest->isTteRevoked())
                ? route('letters.verify', $serviceRequest->verification_token)
                : null,
            'created_at' => $serviceRequest->created_at->translatedFormat('d M Y, H:i'),
            'attachments' => $serviceRequest->attachments->pluck('requirement_name')->values()->all(),
        ];
    }
}
