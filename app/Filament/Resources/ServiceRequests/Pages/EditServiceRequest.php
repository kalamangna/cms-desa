<?php

namespace App\Filament\Resources\ServiceRequests\Pages;

use App\Exceptions\BsreException;
use App\Filament\Resources\ServiceRequests\ServiceRequestResource;
use App\Models\Official;
use App\Models\ServiceRequest;
use App\Models\Setting;
use App\Services\LetterDocumentService;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Throwable;

class EditServiceRequest extends EditRecord
{
    protected static string $resource = ServiceRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('syncCitizenData')
                ->label('Isi Data Warga')
                ->icon('heroicon-o-identification')
                ->color('gray')
                ->visible(fn (): bool => ! $this->record->isTteSigned())
                ->action(function (LetterDocumentService $letterService): void {
                    $this->save(shouldRedirect: false, shouldSendSavedNotification: false);

                    $freshRecord = $this->record->fresh();
                    $payload = $letterService->buildDefaultPayload($freshRecord);
                    $freshRecord->update(['letter_payload' => $payload]);
                    $this->refreshFormData(['letter_payload']);

                    Notification::make()
                        ->success()
                        ->title('Data warga berhasil dimuat')
                        ->send();
                }),

            Action::make('generateDraftPdf')
                ->label('Buat Draf PDF')
                ->icon('heroicon-o-document-text')
                ->color('info')
                ->visible(fn (): bool => (bool) $this->record->service?->is_tte_enabled && ! $this->record->isTteSigned())
                ->action(function (LetterDocumentService $letterService): void {
                    $this->save(shouldRedirect: false, shouldSendSavedNotification: false);

                    try {
                        $letterService->generateDraftPdf($this->record->fresh(), auth()->user());
                        $this->refreshFormData(['letter_number', 'letter_date', 'letter_payload', 'tte_status']);

                        Notification::make()
                            ->success()
                            ->title('Draf PDF surat berhasil dibuat')
                            ->send();
                    } catch (Throwable $e) {
                        Notification::make()
                            ->danger()
                            ->title('Draf PDF gagal dibuat')
                            ->body($e->getMessage())
                            ->send();
                    }
                }),

            Action::make('signWithBsre')
                ->label('Tanda Tangani BSrE')
                ->icon('heroicon-o-shield-check')
                ->color('success')
                ->visible(fn (LetterDocumentService $letterService): bool => $letterService->isGlobalTteEnabled() && (bool) $this->record->service?->is_tte_enabled && ! $this->record->isTteSigned())
                ->modalHeading('Tanda Tangani Surat dengan BSrE')
                ->modalDescription('Passphrase tidak disimpan oleh sistem dan hanya dikirim sekali ke layanan BSrE BSSN.')
                ->modalSubmitActionLabel('Kirim TTE BSrE')
                ->form([
                    Placeholder::make('signer_info')
                        ->label('Pejabat Penanda Tangan')
                        ->content(function (): string {
                            $officialId = Setting::where('key', 'tte_default_signer_official_id')->value('value');
                            $nik = preg_replace('/\D/', '', (string) Setting::where('key', 'tte_default_signer_nik')->value('value'));
                            $official = $officialId ? Official::find($officialId) : null;

                            if (! $official || strlen($nik) !== 16) {
                                return 'Belum diatur pada menu Pengaturan.';
                            }

                            $maskedNik = substr($nik, 0, 6).'******'.substr($nik, -4);

                            return $official->name.' - '.$official->position.' • NIK '.$maskedNik;
                        }),
                    TextInput::make('passphrase')
                        ->label('Passphrase BSrE')
                        ->password()
                        ->revealable()
                        ->required(),
                ])
                ->action(function (array $data, LetterDocumentService $letterService): void {
                    $this->save(shouldRedirect: false, shouldSendSavedNotification: false);

                    $officialId = Setting::where('key', 'tte_default_signer_official_id')->value('value');
                    $signerNik = preg_replace('/\D/', '', (string) Setting::where('key', 'tte_default_signer_nik')->value('value'));
                    $official = $officialId ? Official::find($officialId) : null;

                    if (! $official || strlen($signerNik) !== 16) {
                        Notification::make()
                            ->danger()
                            ->title('Konfigurasi penanda tangan belum lengkap')
                            ->body('Pejabat penanda tangan dan NIK 16 digit wajib diatur di menu Pengaturan.')
                            ->send();

                        return;
                    }

                    try {
                        $letterService->signWithBsre(
                            $this->record->fresh(),
                            $official,
                            $signerNik,
                            (string) $data['passphrase'],
                            auth()->user()
                        );
                        // Refresh the record to ensure latest data (e.g., status, payload) is loaded
                        $this->record = $this->record->fresh();

                        $this->refreshFormData([
                            'letter_number',
                            'letter_date',
                            'letter_payload',
                            'status',
                            'tte_status',
                        ]);

                        Notification::make()
                            ->success()
                            ->title('Surat berhasil ditandatangani melalui BSrE')
                            ->send();
                    } catch (BsreException $e) {
                        Notification::make()
                            ->danger()
                            ->title('Penandatanganan BSrE gagal')
                            ->body($e->getMessage())
                            ->send();
                    } catch (Throwable $e) {
                        Notification::make()
                            ->danger()
                            ->title('Terjadi kendala saat memproses TTE')
                            ->body($e->getMessage())
                            ->send();
                    }
                }),

            Action::make('revokeTte')
                ->label('Cabut Surat TTE')
                ->icon('heroicon-o-no-symbol')
                ->color('danger')
                ->visible(fn (): bool => $this->record->tte_status === ServiceRequest::TTE_SIGNED)
                ->modalHeading('Cabut Keabsahan Surat TTE')
                ->modalDescription('Cabut surat ini? Status verifikasi QR Code akan berubah menjadi dicabut dan warga tidak dapat mengunduhnya.')
                ->modalSubmitActionLabel('Cabut Surat TTE')
                ->form([
                    Textarea::make('reason')
                        ->label('Alasan Pencabutan')
                        ->placeholder('Kesalahan penulisan data atau pembatalan permohonan')
                        ->required()
                        ->rows(3),
                ])
                ->action(function (array $data, LetterDocumentService $letterService): void {
                    $letterService->revoke($this->record, (string) $data['reason'], auth()->user());
                    $this->refreshFormData(['tte_status']);

                    Notification::make()
                        ->warning()
                        ->title('Keabsahan surat TTE berhasil dicabut')
                        ->send();
                }),

            DeleteAction::make()
                ->visible(fn (): bool => ! $this->record->isTteSigned()),
        ];
    }
}
