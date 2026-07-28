<?php

namespace App\Filament\Resources\Announcements\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\DateTimePicker;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;
use Filament\Forms\Set;

class AnnouncementForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')->label('Judul')
                    ->required()
                    ->columnSpanFull(),
                RichEditor::make('content')->label('Konten')
                    ->required()
                    ->columnSpanFull(),
                DateTimePicker::make('published_at')->label('Tanggal Publikasi')
                    ->helperText('Kosongkan untuk langsung terbitkan sekarang, atau atur tanggal/waktu di masa depan untuk menjadwalkan penayangan')
                    ->columnSpanFull(),
            ]);
    }
}
