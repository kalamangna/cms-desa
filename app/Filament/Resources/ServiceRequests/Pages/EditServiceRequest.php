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
use Filament\Forms\Components\Select;
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
                    $payload = $letterService->buildDefaultPayload($this->record);
                    $this->record->update(['letter_payload' => $payload]);
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
                    Select::make('official_id')
                        ->label('Pejabat Penanda Tangan')
                        ->options(fn () => Official::query()->orderBy('order')->get()->mapWithKeys(fn (Official $official) => [
                            $official->id => $official->name.' - '.$official->position,
                        ])->all())
                        ->default(fn () => $this->record->signer_official_id ?: Setting::where('key', 'tte_default_signer_official_id')->value('value'))
                        ->searchable()
                        ->required(),
                    TextInput::make('signer_nik')
                        ->label('NIK Penanda Tangan')
                        ->placeholder('16 digit NIK')
                        ->default(fn () => Setting::where('key', 'tte_default_signer_nik')->value('value'))
                        ->required()
                        ->length(16)
                        ->numeric(),
                    TextInput::make('passphrase')
                        ->label('Passphrase BSrE')
                        ->password()
                        ->revealable()
                        ->required(),
                ])
                ->action(function (array $data, LetterDocumentService $letterService): void {
                    $this->save(shouldRedirect: false, shouldSendSavedNotification: false);

                    $official = Official::find($data['official_id']);
                    if (! $official) {
                        Notification::make()
                            ->danger()
                            ->title('Pejabat penanda tangan tidak ditemukan')
                            ->send();

                        return;
                    }

                    try {
                        $letterService->signWithBsre(
                            $this->record->fresh(),
                            $official,
                            (string) $data['signer_nik'],
                            (string) $data['passphrase'],
                            auth()->user()
                        );

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

            DeleteAction::make(),
        ];
    }
}
