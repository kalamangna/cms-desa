<?php

namespace App\Filament\Resources\Documents\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;
use Filament\Forms\Set;

class DocumentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')->label('Judul')
                    ->placeholder('Contoh: Peraturan Desa No. 3 Tahun 2026 tentang APBDes')
                    ->helperText('Nama resmi dokumen arsip desa.')
                    ->required()
                    ->columnSpanFull(),
                FileUpload::make('file')->label('Berkas')
                    ->helperText('Unggah dokumen (PDF/DOCX, maks 10MB).')
                    ->disk('public')
                    ->directory('documents')
                    ->acceptedFileTypes(['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'])
                    ->required()
                    ->columnSpanFull(),
                Textarea::make('description')->label('Deskripsi')
                    ->placeholder('Contoh: Dokumen rincian peraturan penetapan APBDes tahun anggaran berjalan...')
                    ->helperText('Ringkasan isi dokumen publik ini.')
                    ->columnSpanFull(),
            ]);
    }
}
