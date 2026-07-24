<?php

namespace App\Filament\Resources\ServiceRequests\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;

class ServiceRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('ticket_number')->label('Nomor Tiket')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('nik')->label('NIK')
                    ->searchable(),
                TextColumn::make('name')->label('Nama Pemohon')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('service.title')->label('Layanan')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Menunggu' => 'gray',
                        'Diproses' => 'warning',
                        'Selesai' => 'success',
                        default => 'gray',
                    }),
                TextColumn::make('created_at')->label('Diajukan Pada')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'Menunggu' => 'Menunggu',
                        'Diproses' => 'Diproses',
                        'Selesai' => 'Selesai',
                    ]),
                SelectFilter::make('service_id')
                    ->label('Layanan')
                    ->relationship('service', 'title')
                    ->searchable()
                    ->preload(),
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
                        $statusLabel = match ($record->status) {
                            'Diproses' => 'sedang DIPROSES',
                            'Selesai'  => 'telah SELESAI diproses. Silakan mengambil berkas fisik di Kantor Desa',
                            default    => 'masih MENUNGGU untuk diproses',
                        };
                        $serviceName = $record->service?->title ?? 'layanan yang Anda ajukan';
                        $message = "Halo {$record->name}, permohonan {$serviceName} Anda (Nomor Tiket: {$record->ticket_number}) {$statusLabel}.\n\nTerima kasih.";
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

