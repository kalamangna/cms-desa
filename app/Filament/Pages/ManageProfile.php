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
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Actions\Action;
use App\Models\Setting;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class ManageProfile extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';
    protected static string|\UnitEnum|null $navigationGroup = 'Profil Desa';
    protected static ?string $title = 'Profil Desa';
    protected static ?string $navigationLabel = 'Profil Desa';
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
                Tabs::make('Profil & Wilayah')
                    ->tabs([
                        Tabs\Tab::make('Sejarah & Visi Misi')
                            ->icon('heroicon-o-book-open')
                            ->columns(2)
                            ->components([
                                RichEditor::make('village_history')->label('Sejarah Desa')->columnSpanFull(),
                                TextInput::make('village_vision')->label('Visi Desa')->columnSpanFull(),
                                RichEditor::make('village_mission')->label('Misi Desa')->columnSpanFull(),
                                TextInput::make('village_head_greeting_title')->label('Judul Sambutan Kades')->columnSpanFull(),
                                RichEditor::make('village_head_greeting')->label('Isi Sambutan Kades')->columnSpanFull(),
                            ]),
                        Tabs\Tab::make('Karakteristik & Wilayah')
                            ->icon('heroicon-o-globe-asia-australia')
                            ->columns(2)
                            ->components([
                                Select::make('province_name')
                                    ->label('Provinsi')
                                    ->options(fn () => self::getProvinceOptions())
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->afterStateUpdated(function (Set $set) {
                                        $set('regency_name', null);
                                        $set('district_name', null);
                                    }),
                                Select::make('regency_name')
                                    ->label('Kabupaten')
                                    ->options(fn (Get $get) => self::getRegencyOptions($get('province_name')))
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->afterStateUpdated(function (Set $set) {
                                        $set('district_name', null);
                                    })
                                    ->disabled(fn (Get $get) => empty($get('province_name'))),
                                Select::make('district_name')
                                    ->label('Kecamatan')
                                    ->options(fn (Get $get) => self::getDistrictOptions($get('province_name'), $get('regency_name')))
                                    ->searchable()
                                    ->preload()
                                    ->disabled(fn (Get $get) => empty($get('regency_name'))),
                                TextInput::make('village_area')->label('Luas Wilayah (km²)')->numeric(),
                                TextInput::make('village_population')->label('Jumlah Populasi (Jiwa)'),
                                TextInput::make('village_topography')->label('Topografi Wilayah (misal: Dataran Tinggi)'),
                                TextInput::make('village_dusun_count')->label('Jumlah Dusun')->numeric(),
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
                ->label('Simpan Profil')
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

        Notification::make()
            ->success()
            ->title('Berhasil')
            ->body('Profil, sejarah, dan karakteristik wilayah desa berhasil disimpan.')
            ->send();
    }
    public static function getProvinceOptions(): array
    {
        return Cache::remember('indonesia_provinces', 86400, function () {
            try {
                $response = Http::get('https://www.emsifa.com/api-wilayah-indonesia/api/provinces.json');
                if ($response->successful()) {
                    $list = $response->json();
                    $options = [];
                    foreach ($list as $item) {
                        $name = strtoupper($item['name']);
                        $options[$name] = $name;
                    }
                    asort($options);
                    return $options;
                }
            } catch (\Exception $e) {
                // Ignore
            }
            return [];
        });
    }

    protected static function getProvinceIdByName(string $name): ?string
    {
        $provinces = Cache::remember('indonesia_provinces_raw', 86400, function () {
            try {
                $response = Http::get('https://www.emsifa.com/api-wilayah-indonesia/api/provinces.json');
                return $response->successful() ? $response->json() : [];
            } catch (\Exception $e) {
                return [];
            }
        });

        foreach ($provinces as $prov) {
            if (strtoupper($prov['name']) === strtoupper($name)) {
                return $prov['id'];
            }
        }
        return null;
    }

    public static function getRegencyOptions(?string $provinceName): array
    {
        if (empty($provinceName)) {
            return [];
        }

        $provinceId = self::getProvinceIdByName($provinceName);
        if (!$provinceId) {
            return [];
        }

        return Cache::remember("indonesia_regencies_{$provinceId}", 86400, function () use ($provinceId) {
            try {
                $response = Http::get("https://www.emsifa.com/api-wilayah-indonesia/api/regencies/{$provinceId}.json");
                if ($response->successful()) {
                    $list = $response->json();
                    $options = [];
                    foreach ($list as $item) {
                        $name = strtoupper($item['name']);
                        $options[$name] = $name;
                    }
                    asort($options);
                    return $options;
                }
            } catch (\Exception $e) {
                // Ignore
            }
            return [];
        });
    }

    protected static function getRegencyIdByName(?string $provinceName, string $regencyName): ?string
    {
        if (empty($provinceName)) return null;
        $provinceId = self::getProvinceIdByName($provinceName);
        if (!$provinceId) return null;

        $regencies = Cache::remember("indonesia_regencies_raw_{$provinceId}", 86400, function () use ($provinceId) {
            try {
                $response = Http::get("https://www.emsifa.com/api-wilayah-indonesia/api/regencies/{$provinceId}.json");
                return $response->successful() ? $response->json() : [];
            } catch (\Exception $e) {
                return [];
            }
        });

        foreach ($regencies as $reg) {
            if (strtoupper($reg['name']) === strtoupper($regencyName)) {
                return $reg['id'];
            }
        }
        return null;
    }

    public static function getDistrictOptions(?string $provinceName, ?string $regencyName): array
    {
        if (empty($provinceName) || empty($regencyName)) {
            return [];
        }

        $regencyId = self::getRegencyIdByName($provinceName, $regencyName);
        if (!$regencyId) {
            return [];
        }

        return Cache::remember("indonesia_districts_{$regencyId}", 86400, function () use ($regencyId) {
            try {
                $response = Http::get("https://www.emsifa.com/api-wilayah-indonesia/api/districts/{$regencyId}.json");
                if ($response->successful()) {
                    $list = $response->json();
                    $options = [];
                    foreach ($list as $item) {
                        $name = strtoupper($item['name']);
                        $options[$name] = $name;
                    }
                    asort($options);
                    return $options;
                }
            } catch (\Exception $e) {
                // Ignore
            }
            return [];
        });
    }
}
