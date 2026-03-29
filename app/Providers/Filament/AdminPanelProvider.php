<?php

namespace App\Providers\Filament;

use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use CraftForge\FilamentLanguageSwitcher\FilamentLanguageSwitcherPlugin;
use Filament\Enums\ThemeMode;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Jeffgreco13\FilamentBreezy\BreezyCore;




class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        // dd(get_class_methods($panel));
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->passwordReset()
            ->colors([
                'primary' => Color::Amber,
            ])

            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                // Widgets\AccountWidget::class,
                // Widgets\FilamentInfoWidget::class,
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
                FilamentShieldPlugin::make(),
                FilamentLanguageSwitcherPlugin::make()
                    ->locales(['en', 'nl'])           // optional — if omitted, auto-detects available ones
                    ->rememberLocale(),

                BreezyCore::make()
                    ->myProfile()                      // adds a profile page
                    ->enableTwoFactorAuthentication(
                        force: false,                  // set true to force all users to enable 2FA
                    ),

            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            // ->brandLogo(asset('logo.png'))
            ->brandLogoHeight('40px')
            // ->favicon(asset('dashboard_logo.jpeg'))
            ->sidebarCollapsibleOnDesktop()
            ->breadcrumbs(false)
            ->globalSearch(true)
            // ->resourceCreatePageRedirect('index') // in filament v4
            // ->topBar(false) // hides top bar
            // ->navigation(false) // hides sidebar
            // ->topNavigation() // shows top navigation
            // ->sidebarFullyCollapsibleOnDesktop()
            // ->sidebarWidth('250px')
            // ->font('poppins')
            // ->darkMode(false)
            // ->darkModeBrandLogo(asset('Logo.png'))
            // ->defaultThemeMode(ThemeMode::Dark)
            // ->brandLogo(function () {
            //     return view('Logo.png'); // for custom logo
            // })
            // ->brandName('Halawonen')

            // ->profile()
            // ->simpleProfilePage(false) // to use this you need ->profile()

            // ->spa()
            // ->spaUrlExceptions(['/admin/profile'])
            // ->unSavedChangesAlert()
            // ->databaseTransactions()
        ;
    }
}
