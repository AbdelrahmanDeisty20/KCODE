<?php

namespace App\Providers\Filament;

use App\Filament\Widgets\LatestOrdersWidget;
use App\Filament\Widgets\StatsOverviewWidget;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use App\Filament\Pages\Dashboard;
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
            ->brandName('KCODE')
            ->brandLogo(fn () => new \Illuminate\Support\HtmlString('
                <div style="display: flex; align-items: center; justify-content: center;">
                    <img src="' . asset('images/logo-BfbQ1CpO.svg') . '" alt="KCODE Logo" style="height: 48px; width: auto; max-height: 55px; filter: drop-shadow(0 3px 10px rgba(194, 89, 117, 0.5));" />
                </div>
            '))
            ->brandLogoHeight('3.5rem')
            ->favicon(asset('images/logo-BfbQ1CpO.svg'))
            ->databaseNotifications()
            ->databaseNotificationsPolling('30s')
            ->colors([
                'primary' => [
                    50 => '#fdf4f7',
                    100 => '#f9eaee',
                    200 => '#f5d5e0',
                    300 => '#eba6be',
                    400 => '#e0769a',
                    500 => '#c25975',
                    600 => '#aa3f5d',
                    700 => '#8e304a',
                    800 => '#762b3f',
                    900 => '#642838',
                    950 => '#3a121e',
                ],
                'secondary' => Color::Amber,
            ])
            ->navigationGroups([
                \Filament\Navigation\NavigationGroup::make()
                    ->label(fn () => app()->getLocale() === 'en' ? 'Project & System Settings' : 'عن المشروع وإعدادات النظام'),
                \Filament\Navigation\NavigationGroup::make()
                    ->label(fn () => app()->getLocale() === 'en' ? 'Users & Permissions' : 'إدارة المستخدمين والصلاحيات'),
                \Filament\Navigation\NavigationGroup::make()
                    ->label(fn () => app()->getLocale() === 'en' ? 'Products & Catalog' : 'إدارة المنتجات والكتالوج'),
                \Filament\Navigation\NavigationGroup::make()
                    ->label(fn () => app()->getLocale() === 'en' ? 'Skin Quiz & Assessment Engine' : 'محرك التقييم و Quiz البشرة'),
                \Filament\Navigation\NavigationGroup::make()
                    ->label(fn () => app()->getLocale() === 'en' ? 'Orders & Sales' : 'إدارة المبيعات والطلبات'),
                \Filament\Navigation\NavigationGroup::make()
                    ->label(fn () => app()->getLocale() === 'en' ? 'Loyalty Program & Points' : 'برنامج الولاء والنقاط'),
                \Filament\Navigation\NavigationGroup::make()
                    ->label(fn () => app()->getLocale() === 'en' ? 'Reviews & Ratings' : 'المراجعات والتقييمات'),
                \Filament\Navigation\NavigationGroup::make()
                    ->label(fn () => app()->getLocale() === 'en' ? 'Blog & Articles' : 'المحتوى والمدونة'),
                \Filament\Navigation\NavigationGroup::make()
                    ->label(fn () => app()->getLocale() === 'en' ? 'Reports & Exporter' : 'التقارير وتصدير البيانات'),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
                \App\Filament\Pages\AIChatbot::class,
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

                        /* --- KCODE OFFICIAL TYPOGRAPHY SYSTEM (29LT Zarid Sans AL) --- */
                        @font-face {
                            font-family: "29LT Zarid Sans AL";
                            src: url("/fonts/29LTZaridSansAL-Regular.woff2") format("woff2"),
                                 url("' . asset('fonts/29LTZaridSansAL-Regular.woff2') . '") format("woff2");
                            font-weight: 400;
                            font-style: normal;
                            font-display: swap;
                        }
                        @font-face {
                            font-family: "29LT Zarid Sans AL";
                            src: url("/fonts/29LTZaridSansAL-Medium.woff") format("woff"),
                                 url("' . asset('fonts/29LTZaridSansAL-Medium.woff') . '") format("woff");
                            font-weight: 500;
                            font-style: normal;
                            font-display: swap;
                        }
                        @font-face {
                            font-family: "29LT Zarid Sans AL";
                            src: url("/fonts/29LTZaridSansAL-SemiBold.woff2") format("woff2"),
                                 url("' . asset('fonts/29LTZaridSansAL-SemiBold.woff2') . '") format("woff2");
                            font-weight: 600;
                            font-style: normal;
                            font-display: swap;
                        }

                        :root, html, body, body *, .fi-body, .fi-sidebar, .fi-topbar, .fi-main, .fi-section, .fi-ta, .fi-wi, input, select, textarea, button, span, div, a {
                            --font-sans: "29LT Zarid Sans AL", system-ui, -apple-system, sans-serif !important;
                            font-family: "29LT Zarid Sans AL", system-ui, -apple-system, sans-serif !important;
                        }

                        /* Headings & Main Buttons: SemiBold (600) */
                        h1, h2, h3, h4, h5, h6, .fi-header-heading, .fi-section-header-heading, .fi-modal-heading, .fi-btn-primary, .fi-btn-action {
                            font-weight: 600 !important;
                        }

                        /* Labels, Table Headers, Badges, Navigation: Medium (500) */
                        th, .fi-ta-header-cell, label, .fi-sidebar-item-label, .fi-badge, .fi-stat-label, .fi-breadcrumbs-item {
                            font-weight: 500 !important;
                        }

                        /* Table Body Cells, Inputs, Helper text: Regular (400) */
                        td, .fi-ta-cell, input, select, textarea, p, .fi-help-text {
                            font-weight: 400 !important;
                        }

                        /* Global KCODE Styling Parameters */
                        :root {
                            --kcode-rose-primary: #c25975;
                            --kcode-rose-light: #e5a2b5;
                            --kcode-rose-soft-bg: #f8e7ed;
                            --kcode-rose-glow: rgba(194, 89, 117, 0.25);
                        }

                        /* LIGHT MODE OVERRIDES (Matching Image 1) */
                        html:not(.dark) .fi-layout,
                        html:not(.dark) body {
                            background-color: #faf6f4 !important;
                        }
                        html:not(.dark) .fi-sidebar {
                            background-color: #ffffff !important;
                            border-left-color: rgba(194, 89, 117, 0.12) !important;
                            border-right-color: rgba(194, 89, 117, 0.12) !important;
                        }
                        html:not(.dark) .fi-topbar {
                            background-color: #ffffff !important;
                            border-bottom: 1px solid rgba(194, 89, 117, 0.12) !important;
                        }
                        html:not(.dark) .fi-wi-stats-overview-stat-card,
                        html:not(.dark) .fi-wi-widget,
                        html:not(.dark) .fi-section,
                        html:not(.dark) .fi-ta-content {
                            background-color: #ffffff !important;
                            border: 1px solid rgba(194, 89, 117, 0.14) !important;
                            border-radius: 1.25rem !important;
                            box-shadow: 0 6px 20px -3px rgba(194, 89, 117, 0.07) !important;
                        }
                        html:not(.dark) .fi-wi-stats-overview-stat-card:hover {
                            border-color: rgba(194, 89, 117, 0.35) !important;
                            box-shadow: 0 14px 28px -6px rgba(194, 89, 117, 0.2), 0 0 15px rgba(194, 89, 117, 0.12) !important;
                        }

                        /* DARK MODE OVERRIDES (Matching Image 2) */
                        html.dark .fi-layout,
                        html.dark body {
                            background-color: #0e0e11 !important;
                        }
                        html.dark .fi-sidebar {
                            background-color: #121215 !important;
                            border-left-color: rgba(229, 162, 181, 0.12) !important;
                            border-right-color: rgba(229, 162, 181, 0.12) !important;
                        }
                        html.dark .fi-topbar {
                            background-color: #121215 !important;
                            border-bottom: 1px solid rgba(229, 162, 181, 0.12) !important;
                        }
                        html.dark .fi-wi-stats-overview-stat-card,
                        html.dark .fi-wi-widget,
                        html.dark .fi-section,
                        html.dark .fi-ta-content {
                            background-color: #1a1a20 !important;
                            border: 1px solid rgba(229, 162, 181, 0.12) !important;
                            border-radius: 1.25rem !important;
                            box-shadow: 0 8px 25px -4px rgba(0, 0, 0, 0.35) !important;
                        }
                        html.dark .fi-wi-stats-overview-stat-card:hover {
                            border-color: rgba(229, 162, 181, 0.35) !important;
                            box-shadow: 0 14px 28px -6px rgba(229, 162, 181, 0.22), 0 0 20px rgba(229, 162, 181, 0.15) !important;
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
                        }
                        .fi-wi-stats-overview-stat-card:hover .fi-icon-btn,
                        .fi-wi-stats-overview-stat-card:hover svg {
                            transform: scale(1.15) rotate(6deg) !important;
                            transition: transform 0.3s ease !important;
                        }

                        /* Sidebar Items Hover & Active */
                        .fi-sidebar-item-button {
                            border-radius: 9999px !important;
                            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
                        }
                        .fi-sidebar-item-button:hover {
                            transform: translateX(5px) !important;
                        }

                        /* Table Row Smooth Highlight */
                        .fi-ta-row {
                            transition: all 0.2s ease !important;
                        }
                        html:not(.dark) .fi-ta-row:hover {
                            background-color: rgba(194, 89, 117, 0.05) !important;
                        }
                        html.dark .fi-ta-row:hover {
                            background-color: rgba(229, 162, 181, 0.07) !important;
                        }

                        /* Buttons Interactions & Full Pill Styling */
                        .fi-btn {
                            border-radius: 9999px !important;
                            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
                        }
                        .fi-btn:hover {
                            transform: translateY(-2px) !important;
                            box-shadow: 0 6px 16px -4px rgba(194, 89, 117, 0.35) !important;
                        }
                        .fi-btn:active {
                            transform: translateY(0) scale(0.97) !important;
                        }

                        /* Badges Soft Pulse & Pill Shapes */
                        .fi-badge {
                            border-radius: 9999px !important;
                            transition: all 0.2s ease !important;
                        }
                        .fi-badge:hover {
                            animation: pulseBadge 1.2s infinite ease-in-out !important;
                        }

                        /* --- MULTI-COLOR SVG SIDEBAR ICONS --- */
                        .fi-sidebar-item-icon,
                        .fi-sidebar-item-button svg {
                            width: 1.35rem !important;
                            height: 1.35rem !important;
                            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
                            vertical-align: middle !important;
                        }
                        .fi-sidebar-item:hover .fi-sidebar-item-icon,
                        .fi-sidebar-item-button:hover svg {
                            transform: scale(1.22) rotate(3deg) !important;
                            filter: drop-shadow(0 4px 12px rgba(194, 89, 117, 0.4)) !important;
                        }
                    </style>
                ')
            )
            ->renderHook(
                \Filament\View\PanelsRenderHook::PAGE_START,
                fn (): string => \Illuminate\Support\Facades\Blade::render("@include('filament.hooks.animated_header_banner')")
            )
            ->renderHook(
                \Filament\View\PanelsRenderHook::BODY_END,
                fn (): string => \Illuminate\Support\Facades\Blade::render("@include('filament.hooks.floating_chatbot_widget')")
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
