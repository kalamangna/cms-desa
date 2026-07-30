<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Form;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Schemas\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Actions\Action;
use App\Models\Setting;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\FileUpload;

class ManageSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static string|\UnitEnum|null $navigationGroup = 'Sistem';
    protected static ?string $title = 'Pengaturan';
    protected static ?string $navigationLabel = 'Pengaturan';
    protected static ?int $navigationSort = 1;

    public ?array $data = [];

    public function mount(): void
    {
        $settings = Setting::pluck('value', 'key')->toArray();
        foreach ($settings as $key => $value) {
            if (is_string($value) && str_starts_with($value, '[') && str_ends_with($value, ']')) {
                $decoded = json_decode($value, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $settings[$key] = $decoded;
                }
            }
        }
        $this->form->fill($settings);
    }

    public function form(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema
    {
        return $schema
            ->components([
                Tabs::make('Pengaturan')
                    ->tabs([
                    Tabs\Tab::make('Identitas Desa')
                            ->icon('heroicon-o-building-library')
                            ->columns(2)
                            ->components([
                                TextInput::make('village_name')
                                    ->label('Nama Desa')
                                    ->placeholder('Contoh: Tompobulu')
                                    ->helperText('Nama resmi desa, tanpa kata "Desa".')
                                    ->columnSpanFull(),
                                \Filament\Schemas\Components\Grid::make(3)
                                    ->schema([
                                        Select::make('province_name')
                                            ->label('Provinsi')
                                            ->placeholder('Pilih Provinsi')
                                            ->helperText('Pilih lokasi provinsi desa.')
                                            ->options(fn() => self::getProvinces())
                                            ->searchable()
                                            ->preload()
                                            ->live(),
                                        Select::make('regency_name')
                                            ->label('Kabupaten/Kota')
                                            ->placeholder('Pilih Kabupaten/Kota')
                                            ->helperText('Pilih kabupaten/kota desa.')
                                            ->options(fn($get) => $get('province_name') ? self::getRegencies($get('province_name')) : [])
                                            ->disabled(fn($get) => !$get('province_name'))
                                            ->searchable()
                                            ->preload()
                                            ->live(),
                                        Select::make('district_name')
                                            ->label('Kecamatan')
                                            ->placeholder('Pilih Kecamatan')
                                            ->helperText('Pilih kecamatan desa.')
                                            ->options(fn($get) => $get('regency_name') && $get('province_name') ? self::getDistricts($get('province_name'), $get('regency_name')) : [])
                                            ->disabled(fn($get) => !$get('regency_name'))
                                            ->searchable()
                                            ->preload(),
                                    ])
                                    ->columnSpanFull(),
                            ]),
                        Tabs\Tab::make('Kontak & Lokasi')
                            ->icon('heroicon-o-map-pin')
                            ->columns(2)
                            ->components([
                                TextInput::make('village_email')
                                    ->label('Email Desa')
                                    ->placeholder('Contoh: kontak@tompobulu.desa.id')
                                    ->helperText('Email resmi kantor desa.')
                                    ->email(),
                                TextInput::make('village_phone')
                                    ->label('Nomor Telepon')
                                    ->placeholder('Contoh: 081234567890 atau 0411123456')
                                    ->helperText('Nomor kontak resmi / WhatsApp kantor desa.'),
                                TextInput::make('village_address')
                                    ->label('Alamat Kantor')
                                    ->placeholder('Contoh: Jl. Poros Desa No. 1, Desa Tompobulu')
                                    ->helperText('Alamat lengkap lokasi Kantor Desa.')
                                    ->columnSpanFull(),
                            ]),
                        Tabs\Tab::make('Media Sosial')
                            ->icon('heroicon-o-share')
                            ->columns(3)
                            ->components([
                                TextInput::make('social_facebook')
                                    ->label('Facebook URL')
                                    ->placeholder('https://facebook.com/pemdes.tompobulu')
                                    ->helperText('Tautan halaman Facebook resmi desa.'),
                                TextInput::make('social_instagram')
                                    ->label('Instagram URL')
                                    ->placeholder('https://instagram.com/pemdes.tompobulu')
                                    ->helperText('Tautan akun Instagram resmi desa.'),
                                TextInput::make('social_youtube')
                                    ->label('YouTube URL')
                                    ->placeholder('https://youtube.com/@pemdes.tompobulu')
                                    ->helperText('Tautan channel YouTube resmi desa.'),
                            ]),
                        Tabs\Tab::make('Tampilan & Tema')
                            ->icon('heroicon-o-paint-brush')
                            ->columns(1)
                            ->components([
                                Radio::make('primary_color')
                                    ->label('Warna Primer Website')
                                    ->options([
                                        '#10b981' => new \Illuminate\Support\HtmlString('<span style="display: inline-flex; align-items: center; gap: 8px;"><span style="width: 16px; height: 16px; border-radius: 9999px; display: inline-block; border: 1px solid rgba(0,0,0,0.15); background-color: #10b981;"></span> Emerald (Default)</span>'),
                                        '#14b8a6' => new \Illuminate\Support\HtmlString('<span style="display: inline-flex; align-items: center; gap: 8px;"><span style="width: 16px; height: 16px; border-radius: 9999px; display: inline-block; border: 1px solid rgba(0,0,0,0.15); background-color: #14b8a6;"></span> Teal</span>'),
                                        '#0ea5e9' => new \Illuminate\Support\HtmlString('<span style="display: inline-flex; align-items: center; gap: 8px;"><span style="width: 16px; height: 16px; border-radius: 9999px; display: inline-block; border: 1px solid rgba(0,0,0,0.15); background-color: #0ea5e9;"></span> Sky Blue</span>'),
                                        '#3b82f6' => new \Illuminate\Support\HtmlString('<span style="display: inline-flex; align-items: center; gap: 8px;"><span style="width: 16px; height: 16px; border-radius: 9999px; display: inline-block; border: 1px solid rgba(0,0,0,0.15); background-color: #3b82f6;"></span> Royal Blue</span>'),
                                        '#6366f1' => new \Illuminate\Support\HtmlString('<span style="display: inline-flex; align-items: center; gap: 8px;"><span style="width: 16px; height: 16px; border-radius: 9999px; display: inline-block; border: 1px solid rgba(0,0,0,0.15); background-color: #6366f1;"></span> Indigo</span>'),
                                        '#8b5cf6' => new \Illuminate\Support\HtmlString('<span style="display: inline-flex; align-items: center; gap: 8px;"><span style="width: 16px; height: 16px; border-radius: 9999px; display: inline-block; border: 1px solid rgba(0,0,0,0.15); background-color: #8b5cf6;"></span> Violet</span>'),
                                        '#f43f5e' => new \Illuminate\Support\HtmlString('<span style="display: inline-flex; align-items: center; gap: 8px;"><span style="width: 16px; height: 16px; border-radius: 9999px; display: inline-block; border: 1px solid rgba(0,0,0,0.15); background-color: #f43f5e;"></span> Rose</span>'),
                                        '#f97316' => new \Illuminate\Support\HtmlString('<span style="display: inline-flex; align-items: center; gap: 8px;"><span style="width: 16px; height: 16px; border-radius: 9999px; display: inline-block; border: 1px solid rgba(0,0,0,0.15); background-color: #f97316;"></span> Orange</span>'),
                                        '#f59e0b' => new \Illuminate\Support\HtmlString('<span style="display: inline-flex; align-items: center; gap: 8px;"><span style="width: 16px; height: 16px; border-radius: 9999px; display: inline-block; border: 1px solid rgba(0,0,0,0.15); background-color: #f59e0b;"></span> Amber</span>'),
                                        '#475569' => new \Illuminate\Support\HtmlString('<span style="display: inline-flex; align-items: center; gap: 8px;"><span style="width: 16px; height: 16px; border-radius: 9999px; display: inline-block; border: 1px solid rgba(0,0,0,0.15); background-color: #475569;"></span> Slate</span>'),
                                    ])
                                    ->default('#10b981')
                                    ->required(),
                                TextInput::make('userway_widget_id')
                                    ->label('UserWay Widget ID')
                                    ->placeholder('Contoh: xYz12345')
                                    ->helperText('ID unik dari widget aksesibilitas UserWay.'),
                            ]),
                    ])->columnSpanFull()
            ])
            ->statePath('data');
    }

    public function content(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Form::make([
                    \Filament\Schemas\Components\EmbeddedSchema::make('form'),
                ])
                    ->id('form')
                    ->livewireSubmitHandler('save')
                    ->footer([
                        \Filament\Schemas\Components\Actions::make($this->getFormActions()),
                    ]),
            ]);
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Simpan Pengaturan')
                ->submit('save')
                ->color('primary'),
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();

        foreach ($data as $key => $value) {
            $valueToStore = is_array($value) ? json_encode($value) : $value;
            Setting::updateOrCreate(['key' => $key], ['value' => $valueToStore]);
        }

        // Clear home page cache
        Cache::forget('home_village_head');

        Notification::make()
            ->success()
            ->title('Berhasil')
            ->body('Pengaturan desa berhasil disimpan.')
            ->send();
    }

    public static function getProvinces(): array
    {
        return Cache::remember('provinces_list', 86400, function () {
            try {
                $response = Http::get('https://www.emsifa.com/api-wilayah-indonesia/api/provinces.json');
                if ($response->successful()) {
                    $options = [];
                    foreach ($response->json() as $item) {
                        $name = \Illuminate\Support\Str::title($item['name']);
                        $options[$name] = $name;
                    }
                    asort($options);
                    return $options;
                }
            } catch (\Exception $e) {}
            return ['Sulawesi Selatan' => 'Sulawesi Selatan'];
        });
    }

    public static function getRegencies(?string $provinceName): array
    {
        if (empty($provinceName)) {
            $provinceName = 'Sulawesi Selatan';
        }
        return Cache::remember('regencies_list_' . md5($provinceName), 86400, function () use ($provinceName) {
            try {
                $provResponse = Http::get('https://www.emsifa.com/api-wilayah-indonesia/api/provinces.json');
                $provId = '73'; // Default Sulsel
                if ($provResponse->successful()) {
                    foreach ($provResponse->json() as $p) {
                        if (strcasecmp($p['name'], $provinceName) === 0) {
                            $provId = $p['id'];
                            break;
                        }
                    }
                }

                $response = Http::get("https://www.emsifa.com/api-wilayah-indonesia/api/regencies/{$provId}.json");
                if ($response->successful()) {
                    $options = [];
                    foreach ($response->json() as $item) {
                        $name = \Illuminate\Support\Str::title($item['name']);
                        $options[$name] = $name;
                    }
                    asort($options);
                    return $options;
                }
            } catch (\Exception $e) {}
            return ['Sinjai' => 'Sinjai'];
        });
    }

    public static function getDistricts(?string $provinceName, ?string $regencyName): array
    {
        if (empty($regencyName) || empty($provinceName)) return [];
        return Cache::remember('districts_list_' . md5($provinceName . '_' . $regencyName), 86400, function () use ($provinceName, $regencyName) {
            try {
                $provResponse = Http::get('https://www.emsifa.com/api-wilayah-indonesia/api/provinces.json');
                $provId = '73'; 
                if ($provResponse->successful()) {
                    foreach ($provResponse->json() as $p) {
                        if (strcasecmp($p['name'], $provinceName) === 0) {
                            $provId = $p['id'];
                            break;
                        }
                    }
                }

                $regResponse = Http::get("https://www.emsifa.com/api-wilayah-indonesia/api/regencies/{$provId}.json");
                $regId = '7307';
                if ($regResponse->successful()) {
                    foreach ($regResponse->json() as $r) {
                        $cleanedRegName = preg_replace('/^(kabupaten|kota)\s+/i', '', $regencyName);
                        $cleanedRName = preg_replace('/^(kabupaten|kota)\s+/i', '', $r['name']);
                        if (strcasecmp($cleanedRName, $cleanedRegName) === 0) {
                            $regId = $r['id'];
                            break;
                        }
                    }
                }

                $response = Http::get("https://www.emsifa.com/api-wilayah-indonesia/api/districts/{$regId}.json");
                if ($response->successful()) {
                    $options = [];
                    foreach ($response->json() as $item) {
                        $name = \Illuminate\Support\Str::title($item['name']);
                        $options[$name] = $name;
                    }
                    asort($options);
                    return $options;
                }
            } catch (\Exception $e) {}
            return [];
        });
    }
}
