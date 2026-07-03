<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Form;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Schemas\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use Filament\Actions\Action;
use App\Models\Setting;
use Filament\Notifications\Notification;

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
                                TextInput::make('district_name')->label('Kecamatan'),
                                TextInput::make('regency_name')->label('Kabupaten'),
                                TextInput::make('province_name')->label('Provinsi'),
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
}
