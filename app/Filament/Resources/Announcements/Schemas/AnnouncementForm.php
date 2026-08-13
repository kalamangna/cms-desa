<?php

namespace App\Filament\Resources\Announcements\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class AnnouncementForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')->label('Judul')
                    ->placeholder('Contoh: Himbauan Kebersihan Lingkungan dan Kerja Bakti Desa')
                    ->helperText('Judul ringkas pengumuman resmi.')
                    ->required()
                    ->columnSpanFull(),
                RichEditor::make('content')->label('Konten')
                    ->placeholder('Tuliskan rincian isi pengumuman secara lengkap di sini...')
                    ->helperText('Uraian isi pengumuman atau instruksi resmi.')
                    ->required()
                    ->columnSpanFull(),
                DateTimePicker::make('published_at')->label('Tanggal Publikasi')
                    ->placeholder('Kosongkan untuk terbit sekarang')
                    ->helperText('Isi untuk menjadwalkan publikasi.')
                    ->columnSpanFull(),
            ]);
    }
}
