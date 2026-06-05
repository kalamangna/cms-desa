<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Form;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Schemas\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Actions\Action;
use App\Models\Setting;
use Filament\Notifications\Notification;

class ManageSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static string|\UnitEnum|null $navigationGroup = 'Sistem';
    protected static ?string $title = 'Pengaturan Desa';
    protected static ?string $navigationLabel = 'Pengaturan';
    protected static ?int $navigationSort = 1;
    protected string $view = 'filament.pages.manage-settings';

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
                            ->components([
                                TextInput::make('village_name')->label('Nama Desa')->required(),
                                FileUpload::make('village_logo')->label('Logo Desa')->directory('settings')->image(),
                                TextInput::make('village_head')->label('Kepala Desa')->required(),
                            ]),
                        Tabs\Tab::make('Kontak & Lokasi')
                            ->icon('heroicon-o-map-pin')
                            ->components([
                                TextInput::make('village_email')->label('Email Desa')->email(),
                                TextInput::make('village_phone')->label('Nomor Telepon'),
                                TextInput::make('village_address')->label('Alamat Kantor'),
                            ]),
                        Tabs\Tab::make('Wilayah Administratif')
                            ->icon('heroicon-o-globe-asia-australia')
                            ->components([
                                TextInput::make('district_name')->label('Kecamatan')->required(),
                                TextInput::make('regency_name')->label('Kabupaten')->required(),
                                TextInput::make('province_name')->label('Provinsi')->required(),
                            ]),
                    ])
            ])
            ->statePath('data');
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
