<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\ServiceRequest;
use App\Services\ServiceAttachmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Throwable;

class ServiceRequestController extends Controller
{
    public function __construct(
        private readonly ServiceAttachmentService $attachmentService
    ) {}

    public function store(Request $request): RedirectResponse
    {
        $service = $request->filled('service_id')
            ? Service::with('requirementItems')->find($request->input('service_id'))
            : null;

        $rules = [
            'nik' => ['required', 'numeric', 'digits:16'],
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'regex:/^[0-9+\-\s()]{8,25}$/'],
            'service_id' => ['required', 'exists:services,id'],
            'attachments' => ['nullable', 'array'],
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
        }

        $validated = $request->validate($rules, $messages);

        $clean = fn (?string $v) => $v !== null ? trim(strip_tags(preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $v))) : null;

        $sanitized = [
            'nik' => trim($validated['nik']),
            'name' => $clean($validated['name']),
            'phone' => preg_replace('/[^0-9+]/', '', $validated['phone']),
            'service_id' => $validated['service_id'],
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

            return response()->json([
                'found' => true,
                'ticket_number' => $serviceRequest->ticket_number,
                'name' => $serviceRequest->name,
                'nik_masked' => substr($serviceRequest->nik, 0, 4).'**********',
                'service_title' => $serviceRequest->service?->title ?? 'Layanan Umum',
                'status' => $serviceRequest->status,
                'created_at' => $serviceRequest->created_at->translatedFormat('d M Y, H:i'),
                'attachments' => $serviceRequest->attachments->pluck('requirement_name')->values()->all(),
            ]);
        }

        $services = Service::with('requirementItems')->orderBy('id', 'asc')->get();

        return view('pages.layanan', compact('services', 'serviceRequest'))
            ->with('searched_ticket', $request->ticket_number);
    }
}
