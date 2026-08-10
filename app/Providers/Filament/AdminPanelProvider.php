<?php

namespace App\Providers\Filament;

use App\Filament\Widgets\LatestOrdersWidget;
use App\Filament\Widgets\StatsOverviewWidget;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->brandName('KCODE Admin')
            ->databaseNotifications()
            ->databaseNotificationsPolling('30s')
            ->colors([
                'primary' => Color::Emerald,
                'secondary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                StatsOverviewWidget::class,
                \App\Filament\Widgets\OrdersChartWidget::class,
                \App\Filament\Widgets\ChatbotAnalyticsWidget::class,
                LatestOrdersWidget::class,
                AccountWidget::class,
            ])
            ->renderHook(
                \Filament\View\PanelsRenderHook::HEAD_END,
                fn (): string => \Illuminate\Support\Facades\Blade::render('
                    <style>
                        @keyframes fadeInUp {
                            from { opacity: 0; transform: translateY(16px); }
                            to { opacity: 1; transform: translateY(0); }
                        }
                        @keyframes pulseBadge {
                            0%, 100% { transform: scale(1); opacity: 1; }
                            50% { transform: scale(1.08); opacity: 0.85; }
                        }
                        
                        /* Entrance Animation for Dashboard Cards */
                        .fi-wi-stats-overview-stat-card,
                        .fi-wi-widget,
                        .fi-ta-content {
                            animation: fadeInUp 0.45s cubic-bezier(0.16, 1, 0.3, 1) ease-out forwards !important;
                        }
                        
                        /* Hover Animations for Stat Cards */
                        .fi-wi-stats-overview-stat-card {
                            transition: all 0.35s cubic-bezier(0.34, 1.56, 0.64, 1) !important;
                        }
                        .fi-wi-stats-overview-stat-card:hover {
                            transform: translateY(-6px) scale(1.015) !important;
                            box-shadow: 0 14px 28px -6px rgba(16, 185, 129, 0.22), 0 0 15px rgba(16, 185, 129, 0.15) !important;
                        }
                        .fi-wi-stats-overview-stat-card:hover .fi-icon-btn,
                        .fi-wi-stats-overview-stat-card:hover svg {
                            transform: scale(1.15) rotate(6deg) !important;
                            transition: transform 0.3s ease !important;
                        }

                        /* Sidebar Items Hover Slide */
                        .fi-sidebar-item-button {
                            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
                        }
                        .fi-sidebar-item-button:hover {
                            transform: translateX(5px) !important;
                        }

                        /* Table Row Smooth Highlight */
                        .fi-ta-row {
                            transition: all 0.2s ease !important;
                        }
                        .fi-ta-row:hover {
                            background-color: rgba(16, 185, 129, 0.05) !important;
                        }

                        /* Buttons Interactions */
                        .fi-btn {
                            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
                        }
                        .fi-btn:hover {
                            transform: translateY(-2px) !important;
                            box-shadow: 0 6px 16px -4px rgba(16, 185, 129, 0.3) !important;
                        }
                        .fi-btn:active {
                            transform: translateY(0) scale(0.97) !important;
                        }

                        /* Badges Soft Pulse */
                        .fi-badge {
                            transition: all 0.2s ease !important;
                        }
                        .fi-badge:hover {
                            animation: pulseBadge 1.2s infinite ease-in-out !important;
                        }
                    </style>
                ')
            )
            ->renderHook(
                \Filament\View\PanelsRenderHook::PAGE_START,
                fn (): string => \Illuminate\Support\Facades\Blade::render("@include('filament.hooks.animated_header_banner')")
            )
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
