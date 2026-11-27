<?php

namespace App\Filament\Resources\Complaints\Pages;

use App\Filament\Resources\Complaints\ComplaintResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditComplaint extends EditRecord
{
    protected static string $resource = ComplaintResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('sendWhatsapp')
                ->label('Kirim Notifikasi WA')
                ->icon('heroicon-o-chat-bubble-left-ellipsis')
                ->color('success')
                ->visible(fn (): bool => ! empty($this->record->phone))
                ->url(function (): string {
                    $waNumber = preg_replace('/[^0-9]/', '', (string) ($this->record->phone ?? ''));
                    if (str_starts_with($waNumber, '0')) {
                        $waNumber = '62'.substr($waNumber, 1);
                    }

                    $response = $this->record->response ? "\n\nTanggapan: {$this->record->response}" : '';
                    $statusLabel = match ($this->record->status) {
                        'Diproses' => 'sedang DIPROSES',
                        'Selesai' => 'telah SELESAI ditindaklanjuti',
                        default => 'masih MENUNGGU',
                    };

                    $message = "Halo {$this->record->name}, pengaduan Anda dengan judul \"{$this->record->title}\" (Nomor Tiket: {$this->record->ticket_number}) {$statusLabel}.{$response}\n\nTerima kasih.";

                    return "https://wa.me/{$waNumber}?text=".urlencode($message);
                })
                ->openUrlInNewTab(),

            DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        $this->record->refresh();
    }
}
