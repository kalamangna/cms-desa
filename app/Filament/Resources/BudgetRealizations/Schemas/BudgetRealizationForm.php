<?php

namespace App\Filament\Resources\BudgetRealizations\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class BudgetRealizationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')->label('Judul / Uraian')
                    ->placeholder('Contoh: Dana Desa (DD) atau Pembangunan Jalan Tani')
                    ->helperText('Pos penerimaan atau pengeluaran APBDes.')
                    ->required()
                    ->columnSpanFull(),
                \Filament\Schemas\Components\Grid::make(2)
                    ->schema([
                        Select::make('budget_category_id')->label('Kategori Anggaran')
                            ->placeholder('Pilih Kategori')
                            ->helperText('Kategori APBDes untuk pos anggaran ini.')
                            ->relationship('category', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        TextInput::make('year')->label('Tahun')
                            ->placeholder('Contoh: 2026')
                            ->helperText('Tahun anggaran penetapan APBDes.')
                            ->numeric()
                            ->default(date('Y'))
                            ->required(),
                    ])
                    ->columnSpanFull(),
                \Filament\Schemas\Components\Grid::make(2)
                    ->schema([
                        TextInput::make('budget_amount')->label('Anggaran')
                            ->placeholder('Contoh: 150000000')
                            ->helperText('Jumlah alokasi anggaran yang ditetapkan (Rupiah).')
                            ->numeric()
                            ->prefix('Rp')
                            ->required(),
                        TextInput::make('realization_amount')->label('Realisasi')
                            ->placeholder('Contoh: 120000000')
                            ->helperText('Total realisasi serapan anggaran (Rupiah).')
                            ->numeric()
                            ->prefix('Rp')
                            ->required(),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
