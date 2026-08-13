<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Auth\Login;
use App\Filament\Pages\Cadangan;
use App\Filament\Widgets\VisitSiteWidget;
use App\Models\Setting;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use ShuvroRoy\FilamentSpatieLaravelBackup\FilamentSpatieLaravelBackupPlugin;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login(Login::class)
            ->renderHook(
                PanelsRenderHook::AUTH_LOGIN_FORM_AFTER,
                fn (): string => Blade::render('
                    <div class="text-center mt-6">
                        <x-filament::link
                            href="/"
                            icon="heroicon-m-arrow-left"
                            color="gray"
                            class="text-sm font-medium"
                        >
                            Kembali ke Halaman Utama
                        </x-filament::link>
                    </div>
                '),
            )

            ->brandName(function () {
                try {
                    if (Schema::hasTable('settings')) {
                        $name = Setting::where('key', 'village_name')->value('value');
                        if (! empty($name)) {
                            return 'Desa '.Str::title($name);
                        }
                    }
                } catch (\Throwable $e) {
                }

                return 'Website Desa';
            })
            ->brandLogo('/img/sinjai.png')
            ->brandLogoHeight('2.5rem')
            ->favicon('/img/sinjai.png')
            ->font('Poppins') // Menyelaraskan dengan font-heading Frontend
            ->colors([
                'primary' => Color::Emerald,
                'danger' => Color::Rose,
                'info' => Color::Blue,
                'success' => Color::Emerald,
                'warning' => Color::Orange,
                'gray' => Color::Slate,
            ])
            ->sidebarCollapsibleOnDesktop()
            ->maxContentWidth('full')
            ->globalSearchKeyBindings(['command+k', 'ctrl+k'])
            ->navigationGroups([
                NavigationGroup::make()
                    ->label('Kependudukan'),
                NavigationGroup::make()
                    ->label('Profil'),
                NavigationGroup::make()
                    ->label('Informasi'),
                NavigationGroup::make()
                    ->label('Transparansi'),
                NavigationGroup::make()
                    ->label('Layanan'),
                NavigationGroup::make()
                    ->label('Peta'),
                NavigationGroup::make()
                    ->label('Master'),
                NavigationGroup::make()
                    ->label('Sistem'),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                VisitSiteWidget::class,
                AccountWidget::class,
                FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->plugins([
                FilamentShieldPlugin::make()
                    ->navigationGroup('Sistem')
                    ->navigationLabel('Peran')
                    ->navigationSort(4),
                FilamentSpatieLaravelBackupPlugin::make()
                    ->authorize(fn () => auth()->user()?->hasRole('super_admin') ?? false)
                    ->navigationGroup('Sistem')
                    ->navigationLabel('Cadangan')
                    ->navigationSort(5)
                    ->usingPage(Cadangan::class),
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
