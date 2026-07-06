<?php

namespace App\Filament\Pages\Auth;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Auth\Pages\Login as BaseLogin;
use Illuminate\Validation\ValidationException;

class Login extends BaseLogin
{
    public function getHeading(): string
    {
        return 'Login Sistem';
    }

    public function getSubheading(): ?string
    {
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('settings')) {
                $name = \App\Models\Setting::where('key', 'village_name')->value('value');
                if (!empty($name)) {
                    return 'Pemerintah Desa ' . \Illuminate\Support\Str::title($name);
                }
            }
        } catch (\Throwable $e) {
        }
        return 'Pemerintah Desa Tompobulu';
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getLoginFormComponent(),
                $this->getPasswordFormComponent(),
                $this->getRememberFormComponent(),
            ]);
    }

    protected function getLoginFormComponent(): Component
    {
        return TextInput::make('login')
            ->label('Username')
            ->required()
            ->autocomplete()
            ->autofocus()
            ->extraInputAttributes(['tabindex' => 1]);
    }

    protected function getCredentialsFromFormData(array $data): array
    {
        return [
            'username' => $data['login'],
            'password' => $data['password'],
        ];
    }

    protected function throwFailureValidationException(): never
    {
        throw ValidationException::withMessages([
            'data.login' => __('filament-panels::pages/auth/login.messages.failed'),
        ]);
    }
}
