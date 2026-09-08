<?php

namespace App\Filament\Resources\AuditLogs;

use App\Filament\Resources\AuditLogs\Pages\ListAuditLogs;
use App\Models\AuditLog;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

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
                    ->label('Rincian'),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Audit Log')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('created_at')->label('Waktu')->dateTime('d M Y, H:i:s'),
                        TextEntry::make('user_name')->label('Pengguna'),
                        TextEntry::make('event')->label('Tipe Aksi')->badge(),
                        TextEntry::make('description')->label('Deskripsi')->columnSpanFull(),
                        TextEntry::make('ip_address')->label('IP Address'),
                        TextEntry::make('user_agent')
                            ->label('Perangkat / Browser')
                            ->formatStateUsing(fn (?string $state): string => static::parseUserAgent($state))
                            ->tooltip(fn (?string $state): ?string => $state)
                            ->columnSpan(2),
                    ]),

                Section::make('Rincian Perubahan Data')
                    ->visible(fn (?AuditLog $record): bool => in_array($record?->event, ['created', 'updated', 'deleted']) && (! empty($record?->old_values) || ! empty($record?->new_values)))
                    ->schema([
                        TextEntry::make('changes_summary')
                            ->hiddenLabel()
                            ->html()
                            ->state(fn (AuditLog $record): string => static::renderChangesHtml($record))
                            ->columnSpanFull(),
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

    /**
     * Format nama atribut menjadi label bahasa Indonesia yang ramah pengguna.
     */
    public static function formatAttributeLabel(string $key): string
    {
        return match ($key) {
            'title' => 'Judul',
            'name' => 'Nama',
            'content' => 'Konten / Isi',
            'description' => 'Deskripsi',
            'category' => 'Kategori',
            'category_id' => 'ID Kategori',
            'featured_image' => 'Gambar Utama',
            'image' => 'Foto / Gambar',
            'photo' => 'Pasfoto',
            'published_at' => 'Waktu Publikasi',
            'slug' => 'Slug URL',
            'is_active' => 'Status Aktif',
            'email' => 'Email',
            'username' => 'Username',
            'phone' => 'No. Telepon',
            'address' => 'Alamat',
            'position' => 'Jabatan',
            'order' => 'Urutan',
            'status' => 'Status',
            default => ucwords(str_replace('_', ' ', $key)),
        };
    }

    /**
     * Format nilai atribut agar tidak memuat HTML mentah panjang atau kode JSON berantakan.
     */
    public static function formatAttributeValue(mixed $value): string
    {
        if ($value === null || $value === '') {
            return '<span class="text-slate-400 dark:text-slate-500 italic text-xs">(kosong)</span>';
        }

        if (is_bool($value)) {
            return $value
                ? '<span class="inline-flex px-2 py-0.5 rounded text-[11px] font-bold bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">Ya</span>'
                : '<span class="inline-flex px-2 py-0.5 rounded text-[11px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700">Tidak</span>';
        }

        if (is_array($value)) {
            return '<code class="text-xs text-slate-700 dark:text-slate-300">'.e(json_encode($value, JSON_UNESCAPED_UNICODE)).'</code>';
        }

        $str = (string) $value;
        if (strlen($str) > 120) {
            $clean = trim(preg_replace('/\s+/', ' ', strip_tags($str)));
            $str = Str::limit($clean, 120);
        }

        return e($str);
    }

    /**
     * Render rincian perbandingan perubahan data dalam format tabel HTML bersih & manusiawi.
     */
    public static function renderChangesHtml(AuditLog $record): string
    {
        $oldValues = is_array($record->old_values) ? $record->old_values : [];
        $newValues = is_array($record->new_values) ? $record->new_values : [];
        $ignored = ['remember_token', 'password', 'updated_at', 'created_at', 'deleted_at'];

        if ($record->event === 'updated') {
            $keys = array_unique(array_merge(array_keys($oldValues), array_keys($newValues)));
            $keys = array_diff($keys, $ignored);

            if (empty($keys)) {
                return '<p class="text-xs text-slate-500 italic py-2">Tidak ada perubahan data atribut yang signifikan.</p>';
            }

            $rows = '';
            foreach ($keys as $key) {
                $label = static::formatAttributeLabel($key);
                $oldVal = static::formatAttributeValue($oldValues[$key] ?? null);
                $newVal = static::formatAttributeValue($newValues[$key] ?? null);

                $rows .= "<tr class=\"border-b border-slate-100 dark:border-slate-800/80 hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition\">
                    <td class=\"py-2.5 px-4 font-semibold text-slate-700 dark:text-slate-200 text-xs whitespace-nowrap\">{$label} <span class=\"text-[10px] text-slate-400 font-normal\">({$key})</span></td>
                    <td class=\"py-2.5 px-4 text-xs text-rose-600 dark:text-rose-400 bg-rose-50/30 dark:bg-rose-950/20\">{$oldVal}</td>
                    <td class=\"py-2.5 px-4 text-xs text-emerald-700 dark:text-emerald-400 bg-emerald-50/30 dark:bg-emerald-950/20 font-medium\">{$newVal}</td>
                </tr>";
            }

            return "
            <div class=\"overflow-x-auto rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-sm\">
                <table class=\"w-full text-left text-xs border-collapse\">
                    <thead>
                        <tr class=\"bg-slate-50 dark:bg-slate-800/80 text-slate-500 dark:text-slate-400 font-bold border-b border-slate-200 dark:border-slate-700 uppercase tracking-wider text-[10px]\">
                            <th class=\"py-2.5 px-4\">Atribut / Kolom</th>
                            <th class=\"py-2.5 px-4\">Nilai Sebelum (Lama)</th>
                            <th class=\"py-2.5 px-4\">Nilai Sesudah (Baru)</th>
                        </tr>
                    </thead>
                    <tbody>
                        {$rows}
                    </tbody>
                </table>
            </div>";
        }

        if ($record->event === 'created') {
            $keys = array_diff(array_keys($newValues), $ignored);

            if (empty($keys)) {
                return '<p class="text-xs text-slate-500 italic py-2">Data baru berhasil ditambahkan.</p>';
            }

            $rows = '';
            foreach ($keys as $key) {
                $label = static::formatAttributeLabel($key);
                $val = static::formatAttributeValue($newValues[$key] ?? null);

                $rows .= "<tr class=\"border-b border-slate-100 dark:border-slate-800/80 hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition\">
                    <td class=\"py-2.5 px-4 font-semibold text-slate-700 dark:text-slate-200 text-xs whitespace-nowrap w-1/3\">{$label} <span class=\"text-[10px] text-slate-400 font-normal\">({$key})</span></td>
                    <td class=\"py-2.5 px-4 text-xs text-slate-800 dark:text-slate-200 font-medium\">{$val}</td>
                </tr>";
            }

            return "
            <div class=\"overflow-x-auto rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-sm\">
                <table class=\"w-full text-left text-xs border-collapse\">
                    <thead>
                        <tr class=\"bg-slate-50 dark:bg-slate-800/80 text-slate-500 dark:text-slate-400 font-bold border-b border-slate-200 dark:border-slate-700 uppercase tracking-wider text-[10px]\">
                            <th class=\"py-2.5 px-4\">Atribut / Kolom</th>
                            <th class=\"py-2.5 px-4\">Nilai Data Baru</th>
                        </tr>
                    </thead>
                    <tbody>
                        {$rows}
                    </tbody>
                </table>
            </div>";
        }

        if ($record->event === 'deleted') {
            $keys = array_diff(array_keys($oldValues), $ignored);

            if (empty($keys)) {
                return '<p class="text-xs text-slate-500 italic py-2">Data berhasil dihapus dari sistem.</p>';
            }

            $rows = '';
            foreach ($keys as $key) {
                $label = static::formatAttributeLabel($key);
                $val = static::formatAttributeValue($oldValues[$key] ?? null);

                $rows .= "<tr class=\"border-b border-slate-100 dark:border-slate-800/80 hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition\">
                    <td class=\"py-2.5 px-4 font-semibold text-slate-700 dark:text-slate-200 text-xs whitespace-nowrap w-1/3\">{$label} <span class=\"text-[10px] text-slate-400 font-normal\">({$key})</span></td>
                    <td class=\"py-2.5 px-4 text-xs text-rose-600 dark:text-rose-400 bg-rose-50/20 dark:bg-rose-950/20 font-medium\">{$val}</td>
                </tr>";
            }

            return "
            <div class=\"overflow-x-auto rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-sm\">
                <table class=\"w-full text-left text-xs border-collapse\">
                    <thead>
                        <tr class=\"bg-slate-50 dark:bg-slate-800/80 text-slate-500 dark:text-slate-400 font-bold border-b border-slate-200 dark:border-slate-700 uppercase tracking-wider text-[10px]\">
                            <th class=\"py-2.5 px-4\">Atribut / Kolom</th>
                            <th class=\"py-2.5 px-4\">Nilai Data Terhapus</th>
                        </tr>
                    </thead>
                    <tbody>
                        {$rows}
                    </tbody>
                </table>
            </div>";
        }

        return '<p class="text-xs text-slate-500 italic py-2">Tidak ada rincian atribut data.</p>';
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
