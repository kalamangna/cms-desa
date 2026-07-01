<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Form;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Schemas\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Actions\Action;
use App\Models\Setting;
use Filament\Notifications\Notification;

class ManageSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static string|\UnitEnum|null $navigationGroup = 'Sistem';
    protected static ?string $title = 'Pengaturan Aplikasi';
    protected static ?string $navigationLabel = 'Pengaturan Aplikasi';
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
                                TextInput::make('village_name')->label('Nama Desa')->required(),
                                TextInput::make('village_head')->label('Kepala Desa')->required(),
                                FileUpload::make('village_logo')->label('Logo Desa')->directory('settings')->image()->columnSpanFull(),
                            ]),
                        Tabs\Tab::make('Karakteristik & Wilayah')
                            ->icon('heroicon-o-globe-asia-australia')
                            ->columns(2)
                            ->components([
                                TextInput::make('district_name')->label('Kecamatan')->required(),
                                TextInput::make('regency_name')->label('Kabupaten')->required(),
                                TextInput::make('province_name')->label('Provinsi')->required(),
                                TextInput::make('village_area')->label('Luas Wilayah (km²)')->numeric(),
                                TextInput::make('village_population')->label('Jumlah Populasi (Jiwa)'),
                                TextInput::make('village_topography')->label('Topografi Wilayah (misal: Dataran Tinggi)'),
                                TextInput::make('village_dusun_count')->label('Jumlah Dusun')->numeric(),
                            ]),
                        Tabs\Tab::make('Kontak & Lokasi')
                            ->icon('heroicon-o-map-pin')
                            ->columns(2)
                            ->components([
                                TextInput::make('village_email')->label('Email Desa')->email(),
                                TextInput::make('village_phone')->label('Nomor Telepon'),
                                TextInput::make('village_address')->label('Alamat Kantor')->columnSpanFull(),
                                TextInput::make('village_latitude')->label('Latitude Peta (misal: -5.230000)'),
                                TextInput::make('village_longitude')->label('Longitude Peta (misal: 120.210000)'),
                            ]),
                        Tabs\Tab::make('Media Sosial')
                            ->icon('heroicon-o-share')
                            ->columns(3)
                            ->components([
                                TextInput::make('social_facebook')->label('Facebook URL')->url(),
                                TextInput::make('social_instagram')->label('Instagram URL')->url(),
                                TextInput::make('social_youtube')->label('YouTube URL')->url(),
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

        Notification::make()
            ->success()
            ->title('Berhasil')
            ->body('Pengaturan desa berhasil disimpan.')
            ->send();
    }
}
