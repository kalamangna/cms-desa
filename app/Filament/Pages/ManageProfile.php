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
use Filament\Schemas\Components\Utilities\Get;
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
    protected static string|\UnitEnum|null $navigationGroup = 'Profil';
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
                                RichEditor::make('village_history')->label('Sejarah Desa')
                                    ->placeholder('Tuliskan naskah sejarah asal-usul terbentuknya desa...')
                                    ->helperText('Uraian riwayat terbentuknya desa.')
                                    ->columnSpanFull(),
                                TextInput::make('village_vision')->label('Visi Desa')
                                    ->placeholder('Contoh: Terwujudnya Desa Tompobulu yang Mandiri, Sejahtera, dan Berbudaya')
                                    ->helperText('Rumusan cita-cita utama pembangunan desa.')
                                    ->columnSpanFull(),
                                RichEditor::make('village_mission')->label('Misi Desa')
                                    ->placeholder('Tuliskan poin-poin misi pembangunan desa...')
                                    ->helperText('Langkah-langkah strategis pencapaian visi.')
                                    ->columnSpanFull(),
                                TextInput::make('village_head_greeting_title')->label('Judul Sambutan Kades')
                                    ->placeholder('Contoh: Sambutan Kepala Desa Tompobulu')
                                    ->helperText('Judul pembuka pesan Kepala Desa.')
                                    ->columnSpanFull(),
                                RichEditor::make('village_head_greeting')->label('Isi Sambutan Kades')
                                    ->placeholder('Tuliskan naskah lengkap sambutan hangat Kepala Desa...')
                                    ->helperText('Pesan Kepala Desa kepada warga.')
                                    ->columnSpanFull(),
                            ]),
                        Tabs\Tab::make('Karakteristik & Wilayah')
                            ->icon('heroicon-o-globe-asia-australia')
                            ->columns(2)
                            ->components([
                                TextInput::make('village_area')
                                    ->label('Luas Wilayah')
                                    ->placeholder('Contoh: 12,5')
                                    ->helperText('Luas total wilayah desa.'),
                                Select::make('village_area_unit')
                                    ->label('Satuan Luas')
                                    ->placeholder('Pilih Satuan')
                                    ->helperText('Satuan pengukuran luas wilayah.')
                                    ->options([
                                        'km²' => 'km² (Kilometer Persegi)',
                                        'Ha'  => 'Ha (Hektar)',
                                    ])
                                    ->default('km²')
                                    ->required(),
                                Select::make('village_topography')
                                    ->label('Topografi Wilayah')
                                    ->placeholder('Pilih Topografi')
                                    ->helperText('Bentuk bentang alam wilayah desa.')
                                    ->options([
                                        'Dataran Rendah'  => 'Dataran Rendah',
                                        'Dataran Tinggi'  => 'Dataran Tinggi',
                                        'Pegunungan'      => 'Pegunungan',
                                        'Perbukitan'      => 'Perbukitan',
                                        'Pesisir / Pantai' => 'Pesisir / Pantai',
                                        'Lembah'          => 'Lembah',
                                        'Rawa'            => 'Rawa',
                                        'Kepulauan'       => 'Kepulauan',
                                    ])
                                    ->searchable()
                                    ->native(false),
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
