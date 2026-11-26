@extends('layouts.app')

@section('title', 'Verifikasi Surat | Desa ' . ($site_settings['village_name'] ?? ''))
@section('meta_description', 'Verifikasi keaslian surat elektronik Pemerintah Desa ' . ($site_settings['village_name'] ?? '') . '.')

@section('content')
@php
    $isValid = $serviceRequest->tte_status === \App\Models\ServiceRequest::TTE_SIGNED;
@endphp

<div class="bg-slate-50 dark:bg-slate-950 py-10 sm:py-16 min-h-[70vh]">
    <div class="max-w-xl mx-auto px-4 sm:px-6">
        <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-sm overflow-hidden">
            <div class="px-6 py-6 border-b border-slate-100 dark:border-slate-800 {{ $isValid ? 'bg-emerald-50/60 dark:bg-emerald-950/30' : 'bg-rose-50/60 dark:bg-rose-950/30' }}">
                <div class="flex items-center gap-3.5">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ $isValid ? 'bg-emerald-600 text-white' : 'bg-rose-600 text-white' }}">
                        <i class="fa-solid {{ $isValid ? 'fa-check' : 'fa-ban' }} text-base"></i>
                    </div>
                    <div>
                        <h1 class="text-lg sm:text-xl font-heading font-bold {{ $isValid ? 'text-emerald-900 dark:text-emerald-100' : 'text-rose-900 dark:text-rose-100' }}">
                            {{ $isValid ? 'Dokumen Terverifikasi' : 'Dokumen Dicabut' }}
                        </h1>
                        <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-300">
                            Pemerintah Desa {{ $site_settings['village_name'] ?? '' }}
                        </p>
                    </div>
                </div>

                @if(! $isValid)
                    <p class="mt-3 text-xs text-rose-700 dark:text-rose-300">
                        Dicabut pada {{ $serviceRequest->revoked_at?->translatedFormat('d M Y, H:i') ?? '-' }}.
                        @if($serviceRequest->revocation_reason)
                            Alasan: {{ $serviceRequest->revocation_reason }}
                        @endif
                    </p>
                @endif
            </div>

            <dl class="divide-y divide-slate-100 dark:divide-slate-800 px-6 text-sm">
                <div class="py-3.5 flex flex-col sm:flex-row sm:justify-between gap-1">
                    <dt class="text-slate-500 dark:text-slate-400">Nomor Surat</dt>
                    <dd class="font-mono font-semibold text-slate-900 dark:text-slate-100 sm:text-right">{{ $serviceRequest->letter_number ?: '-' }}</dd>
                </div>
                <div class="py-3.5 flex flex-col sm:flex-row sm:justify-between gap-1">
                    <dt class="text-slate-500 dark:text-slate-400">Jenis Surat</dt>
                    <dd class="font-medium text-slate-900 dark:text-slate-100 sm:text-right">{{ $serviceRequest->service?->title ?? '-' }}</dd>
                </div>
                <div class="py-3.5 flex flex-col sm:flex-row sm:justify-between gap-1">
                    <dt class="text-slate-500 dark:text-slate-400">Pemohon</dt>
                    <dd class="font-medium text-slate-900 dark:text-slate-100 sm:text-right">{{ $serviceRequest->name }}</dd>
                </div>
                <div class="py-3.5 flex flex-col sm:flex-row sm:justify-between gap-1">
                    <dt class="text-slate-500 dark:text-slate-400">Penanda Tangan</dt>
                    <dd class="font-medium text-slate-900 dark:text-slate-100 sm:text-right">
                        {{ $serviceRequest->signer_name ?: '-' }}
                        @if($serviceRequest->signer_position)
                            <span class="block text-xs text-slate-500 dark:text-slate-400">{{ $serviceRequest->signer_position }}</span>
                        @endif
                    </dd>
                </div>
                <div class="py-3.5 flex flex-col sm:flex-row sm:justify-between gap-1">
                    <dt class="text-slate-500 dark:text-slate-400">Waktu TTE</dt>
                    <dd class="font-medium text-slate-900 dark:text-slate-100 sm:text-right">{{ $serviceRequest->signed_at?->translatedFormat('d F Y, H:i') ?? '-' }}</dd>
                </div>
                <div class="py-3.5 space-y-1">
                    <dt class="text-slate-500 dark:text-slate-400">Hash SHA-256</dt>
                    <dd class="break-all font-mono text-xs text-slate-600 dark:text-slate-300">{{ $serviceRequest->signed_pdf_hash ?: '-' }}</dd>
                </div>
            </dl>
        </div>
    </div>
</div>
@endsection
