<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceRequestAttachment;
use App\Services\ServiceAttachmentService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ServiceRequestAttachmentController extends Controller
{
    public function __construct(
        private readonly ServiceAttachmentService $attachmentService
    ) {}

    public function preview(Request $request, ServiceRequestAttachment $attachment): BinaryFileResponse
    {
        $this->authorizeAccess($request, $attachment);

        $fullPath = $this->attachmentService->resolveSafeAbsolutePath($attachment);
        abort_unless($fullPath !== null, 404);

        return response()->file($fullPath, [
            'Content-Type' => $attachment->mime_type ?: 'application/octet-stream',
            'Content-Disposition' => 'inline; filename="'.addslashes(basename($attachment->original_name)).'"',
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0, private',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    public function download(Request $request, ServiceRequestAttachment $attachment): BinaryFileResponse
    {
        $this->authorizeAccess($request, $attachment);

        $fullPath = $this->attachmentService->resolveSafeAbsolutePath($attachment);
        abort_unless($fullPath !== null, 404);

        $extension = pathinfo($attachment->file_path, PATHINFO_EXTENSION);
        $baseName = pathinfo($attachment->original_name, PATHINFO_FILENAME) ?: 'berkas';
        $downloadName = $baseName.($extension ? '.'.$extension : '');

        return response()->download($fullPath, $downloadName, [
            'Cache-Control' => 'no-store, private',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    private function authorizeAccess(Request $request, ServiceRequestAttachment $attachment): void
    {
        $user = $request->user();
        $serviceRequest = $attachment->serviceRequest;

        $allowed = $user !== null
            && $serviceRequest !== null
            && (
                $user->hasRole('super_admin')
                || $user->can('view', $serviceRequest)
                || $user->can('update', $serviceRequest)
            );

        abort_unless($allowed, 403);
    }
}
