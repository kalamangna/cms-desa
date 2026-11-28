<?php

namespace App\Filament\Resources\ServiceRequests\Pages;

use App\Exceptions\BsreException;
use App\Filament\Resources\ServiceRequests\ServiceRequestResource;
use App\Models\Citizen;
use App\Models\Official;
use App\Models\Service;
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
                ->label('Isi Data')
                ->icon('heroicon-o-identification')
                ->color('gray')
                ->visible(fn (): bool => ! $this->record->isTteSigned() && $this->isApplicantRegistered())
                ->action(function (LetterDocumentService $letterService): void {
                    $this->save(shouldRedirect: false, shouldSendSavedNotification: false);

                    $freshRecord = $this->record->fresh();
                    $found = $letterService->syncCitizenPayload($freshRecord);
                    $this->reloadRecord();
                    $this->refreshFormData(['letter_payload']);

                    if ($found) {
                        Notification::make()
                            ->success()
                            ->title('Data warga berhasil dimuat')
                            ->send();

                        return;
                    }

                    Notification::make()
                        ->warning()
                        ->title('Data warga tidak ditemukan')
                        ->body('NIK belum terdaftar pada data kependudukan. Lengkapi variabel surat secara manual.')
                        ->send();
                }),

            Action::make('generateDraftPdf')
                ->label('Buat Draf')
                ->icon('heroicon-o-document-text')
                ->color('info')
                ->visible(fn (): bool => $this->isSelectedServiceTteEnabled() && ! $this->record->isTteSigned() && ! $this->isRejected())
                ->action(function (LetterDocumentService $letterService): void {
                    $this->save(shouldRedirect: false, shouldSendSavedNotification: false);

                    try {
                        $letterService->generateDraftPdf($this->record->fresh(), auth()->user());
                        $this->reloadRecord();
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
                ->label('Tandatangani')
                ->icon('heroicon-o-shield-check')
                ->color('success')
                ->visible(fn (LetterDocumentService $letterService): bool => $letterService->isGlobalTteEnabled() && $this->isSelectedServiceTteEnabled() && ! $this->record->isTteSigned() && ! $this->isRejected())
                ->modalHeading('Tandatangani Surat')
                ->modalDescription('Passphrase tidak disimpan oleh sistem dan hanya dikirim sekali ke layanan BSrE BSSN.')
                ->modalSubmitActionLabel('Tandatangani')
                ->form([
                    Placeholder::make('bsre_mock_notice')
                        ->label('Mode Simulasi Aktif')
                        ->content('Mode simulasi BSrE sedang aktif. PDF yang dihasilkan tidak memiliki tanda tangan digital kriptografis BSrE nyata.')
                        ->visible(fn (): bool => (bool) config('bsre.mock')),
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
                        $this->reloadRecord();

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
                ->label('Cabut TTE')
                ->icon('heroicon-o-no-symbol')
                ->color('danger')
                ->visible(fn (): bool => $this->record->tte_status === ServiceRequest::TTE_SIGNED)
                ->modalHeading('Cabut Keabsahan Surat TTE')
                ->modalDescription('Cabut surat ini? Status verifikasi QR Code akan berubah menjadi dicabut dan warga tidak dapat mengunduhnya.')
                ->modalSubmitActionLabel('Cabut TTE')
                ->form([
                    Textarea::make('reason')
                        ->label('Alasan Pencabutan')
                        ->placeholder('Kesalahan penulisan data atau pembatalan permohonan')
                        ->required()
                        ->rows(3),
                ])
                ->action(function (array $data, LetterDocumentService $letterService): void {
                    $letterService->revoke($this->record, (string) $data['reason'], auth()->user());
                    $this->reloadRecord();
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

    protected function afterSave(): void
    {
        $this->reloadRecord();
    }

    private function reloadRecord(): void
    {
        // Gunakan refresh() in-place agar referensi record di cachedSchemas Filament ikut terbarui
        $this->record->refresh();
        $this->record->load(['service', 'attachments']);
    }

    private function isSelectedServiceTteEnabled(): bool
    {
        $selectedServiceId = $this->data['service_id'] ?? null;
        if (blank($selectedServiceId) || (int) $selectedServiceId === (int) $this->record->service_id) {
            return (bool) $this->record->service?->is_tte_enabled;
        }

        return (bool) Service::whereKey($selectedServiceId)->value('is_tte_enabled');
    }

    private function isRejected(): bool
    {
        return ($this->data['status'] ?? $this->record->status) === 'Ditolak';
    }

    private function isApplicantRegistered(): bool
    {
        $nik = preg_replace('/\D/', '', (string) ($this->data['nik'] ?? $this->record->nik ?? ''));

        return strlen($nik) === 16 && Citizen::where('nik', $nik)->exists();
    }
}
