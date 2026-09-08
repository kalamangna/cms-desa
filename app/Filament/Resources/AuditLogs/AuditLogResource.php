<?php

namespace App\Filament\Resources\AuditLogs;

use App\Filament\Resources\AuditLogs\Pages\ListAuditLogs;
use App\Models\AuditLog;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class AuditLogResource extends Resource
{
    protected static ?string $model = AuditLog::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Sistem';

    protected static ?string $navigationLabel = 'Audit Log';

    protected static ?string $modelLabel = 'Audit Log';

    protected static ?int $navigationSort = 6;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    public static function canViewAny(): bool
    {
        $user = auth()->user();
        if (! $user) {
            return false;
        }

        return $user->hasRole('super_admin') ||
               $user->hasRole('Super Admin') ||
               $user->hasRole('superadmin') ||
               $user->id === 1;
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Waktu')
                    ->dateTime('d M Y, H:i:s')
                    ->sortable(),

                Tables\Columns\TextColumn::make('user_name')
                    ->label('Pengguna')
                    ->searchable()
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('event')
                    ->label('Aksi')
                    ->badge()
                    ->colors([
                        'success' => 'created',
                        'warning' => 'updated',
                        'danger' => 'deleted',
                        'info' => 'login',
                        'gray' => 'logout',
                    ])
                    ->formatStateUsing(fn (string $state): string => strtoupper($state)),

                Tables\Columns\TextColumn::make('description')
                    ->label('Deskripsi Log')
                    ->searchable()
                    ->wrap(),

                Tables\Columns\TextColumn::make('ip_address')
                    ->label('IP Address')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                ViewAction::make()
                    ->label('Rincian')
                    ->modalHeading('Rincian Audit Log')
                    ->modalWidth(Width::Large),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Audit Log')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('created_at')->label('Waktu')->dateTime('d M Y, H:i:s'),
                        TextEntry::make('user_name')->label('Pengguna'),
                        TextEntry::make('event')->label('Tipe Aksi')->badge(),
                        TextEntry::make('ip_address')->label('IP Address'),
                        TextEntry::make('user_agent')
                            ->label('Perangkat / Browser')
                            ->formatStateUsing(fn (?string $state): string => static::parseUserAgent($state))
                            ->tooltip(fn (?string $state): ?string => $state)
                            ->columnSpanFull(),
                        TextEntry::make('description')->label('Deskripsi')->columnSpanFull(),
                    ]),
            ]);
    }

    /**
     * Parsing user agent string menjadi format nama browser dan OS yang manusiawi.
     */
    public static function parseUserAgent(?string $userAgent): string
    {
        if (! $userAgent) {
            return '-';
        }

        $platform = match (true) {
            str_contains($userAgent, 'Android') => 'Android',
            str_contains($userAgent, 'iPhone') || str_contains($userAgent, 'iPad') => 'iOS',
            str_contains($userAgent, 'Windows') => 'Windows',
            str_contains($userAgent, 'Macintosh') || str_contains($userAgent, 'Mac OS') => 'macOS',
            str_contains($userAgent, 'Linux') => 'Linux',
            default => 'Lainnya',
        };

        $browser = match (true) {
            str_contains($userAgent, 'Edg/') => 'Edge',
            str_contains($userAgent, 'Chrome/') => 'Chrome',
            str_contains($userAgent, 'Firefox/') => 'Firefox',
            str_contains($userAgent, 'Safari/') => 'Safari',
            str_contains($userAgent, 'Opera/') || str_contains($userAgent, 'OPR/') => 'Opera',
            default => 'Browser Web',
        };

        return "{$browser} ({$platform})";
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAuditLogs::route('/'),
        ];
    }

    public static function getPluralModelLabel(): string
    {
        return static::getModelLabel();
    }
}
