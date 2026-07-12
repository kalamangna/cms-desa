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
use Filament\Forms\Get;
use Filament\Actions\Action;
use App\Models\Setting;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Filament\Forms\Components\Radio;

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
                                TextInput::make('village_name')->label('Nama Desa')->columnSpanFull(),
                                \Filament\Schemas\Components\Grid::make(3)
                                    ->schema([
                                        Select::make('province_name')
                                            ->label('Provinsi')
                                            ->options(fn() => self::getProvinces())
                                            ->searchable()
                                            ->preload()
                                            ->live(),
                                        Select::make('regency_name')
                                            ->label('Kabupaten/Kota')
                                            ->options(fn($get) => $get('province_name') ? self::getRegencies($get('province_name')) : [])
                                            ->disabled(fn($get) => !$get('province_name'))
                                            ->searchable()
                                            ->preload()
                                            ->live(),
                                        Select::make('district_name')
                                            ->label('Kecamatan')
                                            ->options(fn($get) => $get('regency_name') ? self::getDistricts($get('regency_name')) : [])
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
                                TextInput::make('village_email')->label('Email Desa')->email(),
                                TextInput::make('village_phone')->label('Nomor Telepon'),
                                TextInput::make('village_address')->label('Alamat Kantor')->columnSpanFull(),
                                TextInput::make('village_latitude')
                                    ->label('Latitude Kantor Desa')
                                    ->helperText('Koordinat garis lintang Kantor Desa (contoh: -5.230000) untuk SEO & peta spasial.'),
                                TextInput::make('village_longitude')
                                    ->label('Longitude Kantor Desa')
                                    ->helperText('Koordinat garis bujur Kantor Desa (contoh: 120.210000) untuk SEO & peta spasial.'),
                            ]),
                        Tabs\Tab::make('Media Sosial')
                            ->icon('heroicon-o-share')
                            ->columns(3)
                            ->components([
                                TextInput::make('social_facebook')->label('Facebook URL'),
                                TextInput::make('social_instagram')->label('Instagram URL'),
                                TextInput::make('social_youtube')->label('YouTube URL'),
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
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
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

    public static function getDistricts(?string $regencyName): array
    {
        if (empty($regencyName)) {
            $regencyName = 'Sinjai';
        }
        return Cache::remember('districts_list_' . md5($regencyName), 86400, function () use ($regencyName) {
            try {
                $regResponse = Http::get('https://www.emsifa.com/api-wilayah-indonesia/api/regencies/73.json');
                $regId = '7307'; // Default Sinjai
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
