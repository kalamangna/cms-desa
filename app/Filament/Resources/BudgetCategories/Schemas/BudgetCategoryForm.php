<?php

namespace App\Filament\Resources\BudgetCategories\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class BudgetCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')->label('Nama Kategori')
                    ->placeholder('Contoh: Pendapatan, Belanja, atau Pembiayaan')
                    ->helperText('Nama kelompok utama transaksi APBDes.')
                    ->required()
                    ->columnSpanFull(),
            ]);
    }
}
