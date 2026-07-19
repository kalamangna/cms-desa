<?php

namespace App\Filament\Resources\Complaints\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class ComplaintsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('ticket_number')->label('Nomor Tiket')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')->label('Pengirim')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('phone')->label('Nomor WA')
                    ->searchable(),
                TextColumn::make('title')->label('Judul Pengaduan')
                    ->searchable()
                    ->limit(30),
                TextColumn::make('status')->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Menunggu' => 'gray',
                        'Diproses' => 'warning',
                        'Selesai' => 'success',
                        default => 'gray',
                    }),
                TextColumn::make('created_at')->label('Tanggal')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('whatsapp')
                    ->label('Kirim WA')
                    ->icon('heroicon-o-chat-bubble-left-ellipsis')
                    ->color('success')
                    ->url(function ($record): string {
                        $waNumber = preg_replace('/[^0-9]/', '', $record->phone ?? '');
                        if (str_starts_with($waNumber, '0')) {
                            $waNumber = '62' . substr($waNumber, 1);
                        }
                        $response = $record->response ? "\n\nTanggapan: {$record->response}" : '';
                        $statusLabel = match ($record->status) {
                            'Diproses' => 'sedang DIPROSES',
                            'Selesai'  => 'telah SELESAI ditindaklanjuti',
                            default    => 'masih MENUNGGU',
                        };
                        $message = "Halo {$record->name}, pengaduan Anda dengan judul \"{$record->title}\" (Nomor Tiket: {$record->ticket_number}) {$statusLabel}.{$response}\n\nTerima kasih.";
                        return "https://wa.me/{$waNumber}?text=" . urlencode($message);
                    })
                    ->openUrlInNewTab()
                    ->visible(fn ($record): bool => ! empty($record->phone)),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

