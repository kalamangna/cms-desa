@extends('layouts.app')

@section('title', 'Verifikasi Surat Elektronik | Desa ' . ($site_settings['village_name'] ?? ''))
@section('meta_description', 'Verifikasi keaslian dokumen surat tanda tangan elektronik Pemerintah Desa ' . ($site_settings['village_name'] ?? '') . '.')

@section('content')
@php
    $isValid = $serviceRequest->tte_status === \App\Models\ServiceRequest::TTE_SIGNED;
@endphp

<div class="bg-slate-900 py-14 sm:py-20">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <span class="inline-flex items-center gap-2 rounded-full bg-primary-500/15 px-3 py-1 text-xs font-bold uppercase tracking-wider text-primary-400">
            <i class="fa-solid fa-shield-halved"></i> Verifikasi Dokumen TTE
        </span>
        <h1 class="mt-4 text-3xl sm:text-4xl font-heading font-black tracking-tight text-white">
            Keaslian Surat Elektronik
        </h1>
        <p class="mt-2 text-sm sm:text-base text-slate-300">
            Pemerintah Desa {{ $site_settings['village_name'] ?? '' }}
        </p>
    </div>
</div>

<div class="bg-slate-50 dark:bg-slate-950 py-10 sm:py-14 min-h-[60vh]">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        <div class="rounded-3xl border {{ $isValid ? 'border-emerald-200 dark:border-emerald-900 bg-emerald-50/80 dark:bg-emerald-950/40' : 'border-rose-200 dark:border-rose-900 bg-rose-50/80 dark:bg-rose-950/40' }} p-6 sm:p-8">
            <div class="flex items-start gap-4">
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl {{ $isValid ? 'bg-emerald-600 text-white' : 'bg-rose-600 text-white' }}">
                    <i class="fa-solid {{ $isValid ? 'fa-circle-check' : 'fa-ban' }} text-xl"></i>
                </div>
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider {{ $isValid ? 'text-emerald-700 dark:text-emerald-300' : 'text-rose-700 dark:text-rose-300' }}">
                        Status Dokumen
                    </p>
                    <h2 class="mt-1 text-xl sm:text-2xl font-heading font-black {{ $isValid ? 'text-emerald-900 dark:text-emerald-100' : 'text-rose-900 dark:text-rose-100' }}">
                        {{ $isValid ? 'Sah dan Terverifikasi' : 'Dicabut dan Tidak Berlaku' }}
                    </h2>
                    <p class="mt-1 text-sm {{ $isValid ? 'text-emerald-800 dark:text-emerald-200' : 'text-rose-800 dark:text-rose-200' }}">
                        @if($isValid)
                            Dokumen ini diterbitkan secara resmi dan ditandatangani secara elektronik melalui BSrE BSSN.
                        @else
                            Keabsahan dokumen ini telah dicabut oleh Pemerintah Desa pada {{ $serviceRequest->revoked_at?->translatedFormat('d M Y, H:i') }}.
                            @if($serviceRequest->revocation_reason)
                                Alasan: {{ $serviceRequest->revocation_reason }}
                            @endif
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <div class="rounded-3xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 sm:p-8 shadow-sm space-y-6">
            <h3 class="text-base font-heading font-extrabold text-slate-900 dark:text-slate-100 border-b border-slate-100 dark:border-slate-800 pb-4">
                Metadata Dokumen
            </h3>

            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-5 text-sm">
                <div>
                    <dt class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Nomor Surat</dt>
                    <dd class="mt-1 font-mono font-bold text-slate-900 dark:text-slate-100">{{ $serviceRequest->letter_number ?: '-' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Jenis Layanan</dt>
                    <dd class="mt-1 font-semibold text-slate-900 dark:text-slate-100">{{ $serviceRequest->service?->title ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Nama Pemohon</dt>
                    <dd class="mt-1 font-semibold text-slate-900 dark:text-slate-100">{{ $serviceRequest->name }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">NIK Pemohon</dt>
                    <dd class="mt-1 font-mono font-semibold text-slate-900 dark:text-slate-100">{{ $serviceRequest->maskedNik() }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Tanggal Surat</dt>
                    <dd class="mt-1 font-semibold text-slate-900 dark:text-slate-100">{{ $serviceRequest->letter_date?->translatedFormat('d F Y') ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Waktu Tanda Tangan</dt>
                    <dd class="mt-1 font-semibold text-slate-900 dark:text-slate-100">{{ $serviceRequest->signed_at?->translatedFormat('d F Y, H:i') ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Penanda Tangan</dt>
                    <dd class="mt-1 font-semibold text-slate-900 dark:text-slate-100">{{ $serviceRequest->signer_name ?: '-' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Jabatan Penanda Tangan</dt>
                    <dd class="mt-1 font-semibold text-slate-900 dark:text-slate-100">{{ $serviceRequest->signer_position ?: '-' }}</dd>
                </div>
                <div class="sm:col-span-2">
                    <dt class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Hash SHA-256 Berkas PDF</dt>
                    <dd class="mt-1 break-all rounded-xl bg-slate-50 dark:bg-slate-800 px-3 py-2 font-mono text-xs text-slate-700 dark:text-slate-300">
                        {{ $serviceRequest->signed_pdf_hash ?: '-' }}
                    </dd>
                </div>
            </dl>
        </div>
    </div>
</div>
@endsection
