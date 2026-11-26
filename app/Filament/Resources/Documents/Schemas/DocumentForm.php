<?php

namespace App\Filament\Resources\Documents\Schemas;

use App\Models\Document;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class DocumentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')->label('Judul')
                    ->placeholder('Peraturan Desa Nomor 3 Tahun 2026 tentang APBDes')
                    ->helperText('Nama resmi dokumen arsip desa.')
                    ->required()
                    ->columnSpanFull(),
                Select::make('category')->label('Jenis Dokumen')
                    ->options(Document::CATEGORY_OPTIONS)
                    ->placeholder('Pilih jenis dokumen')
                    ->helperText('Kategori produk hukum atau arsip administrasi desa.'),
                Select::make('information_classification')->label('Klasifikasi Informasi Publik')
                    ->options(Document::INFORMATION_CLASSIFICATION_OPTIONS)
                    ->placeholder('Pilih klasifikasi informasi')
                    ->helperText('Klasifikasi keterbukaan informasi publik sesuai prinsip PPID Desa.'),
                TextInput::make('document_number')->label('Nomor Dokumen')
                    ->placeholder('03 Tahun 2026')
                    ->maxLength(100),
                TextInput::make('year')->label('Tahun Penetapan')
                    ->numeric()
                    ->minValue(1990)
                    ->maxValue((int) date('Y') + 1)
                    ->placeholder((string) date('Y')),
                DatePicker::make('published_date')->label('Tanggal Penetapan')
                    ->native(false)
                    ->displayFormat('d F Y')
                    ->columnSpanFull(),
                FileUpload::make('file')->label('Berkas')
                    ->helperText('Unggah berkas PDF, Word, atau Excel maksimal 10 MB. Pastikan berkas tidak memuat NIK atau data pribadi warga.')
                    ->disk('public')
                    ->directory('documents')
                    ->acceptedFileTypes(['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'])
                    ->required()
                    ->columnSpanFull(),
                Textarea::make('description')->label('Deskripsi')
                    ->placeholder('Ringkasan pokok aturan atau isi dokumen penetapan APBDes tahun berjalan')
                    ->helperText('Ringkasan isi dokumen publik ini.')
                    ->columnSpanFull(),
            ]);
    }
}
