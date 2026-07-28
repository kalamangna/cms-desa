<?php

namespace App\Filament\Resources\Services\Schemas;

use Filament\Schemas\Schema;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\RichEditor;
use Illuminate\Support\Str;
use Filament\Forms\Set;

class ServiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')->label('Nama Layanan')
                    ->placeholder('Contoh: Surat Keterangan Usaha (SKU)')
                    ->helperText('Nama resmi jenis layanan surat desa.')
                    ->required()
                    ->columnSpanFull(),
                Textarea::make('description')->label('Deskripsi Singkat')
                    ->placeholder('Contoh: Surat keterangan resmi dari pemerintah desa untuk keperluan pembukaan usaha...')
                    ->helperText('Kegunaan dan manfaat layanan surat ini.')
                    ->required()
                    ->columnSpanFull(),
                RichEditor::make('requirements')->label('Persyaratan / Prosedur')
                    ->placeholder('Tuliskan rincian berkas persyaratan (seperti FC KTP, KK, dll) dan tahapan pengajuan...')
                    ->helperText('Persyaratan berkas dan alur pengajuan.')
                    ->columnSpanFull(),
            ]);
    }
}
