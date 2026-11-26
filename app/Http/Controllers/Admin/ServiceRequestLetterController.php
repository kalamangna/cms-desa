<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceRequest;
use App\Services\LetterDocumentService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ServiceRequestLetterController extends Controller
{
    public function __construct(
        private readonly LetterDocumentService $letterDocumentService
    ) {}

    public function preview(Request $request, ServiceRequest $serviceRequest, string $type): BinaryFileResponse
    {
        $this->authorizeAccess($request, $serviceRequest);
        abort_unless(in_array($type, ['draft', 'signed'], true), 404);

        $fullPath = $this->letterDocumentService->resolveSafePdfPath($serviceRequest, $type);
        abort_unless($fullPath !== null, 404);

        $filename = $this->letterDocumentService->buildDownloadFilename($serviceRequest, $type);

        return response()->file($fullPath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$filename.'"',
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0, private',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    public function download(Request $request, ServiceRequest $serviceRequest, string $type): BinaryFileResponse
    {
        $this->authorizeAccess($request, $serviceRequest);
        abort_unless(in_array($type, ['draft', 'signed'], true), 404);

        $fullPath = $this->letterDocumentService->resolveSafePdfPath($serviceRequest, $type);
        abort_unless($fullPath !== null, 404);

        $filename = $this->letterDocumentService->buildDownloadFilename($serviceRequest, $type);

        return response()->download($fullPath, $filename, [
            'Cache-Control' => 'no-store, private',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    private function authorizeAccess(Request $request, ServiceRequest $serviceRequest): void
    {
        $user = $request->user();

        $allowed = $user !== null && (
            $user->hasRole('super_admin')
            || $user->can('view', $serviceRequest)
            || $user->can('update', $serviceRequest)
        );

        abort_unless($allowed, 403);
    }
}
