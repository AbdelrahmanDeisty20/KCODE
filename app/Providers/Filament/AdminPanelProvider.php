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
                    50 => '#f5f0f3',
                    100 => '#ebdbe3',
                    200 => '#dbb8cb',
                    300 => '#c489ab',
                    400 => '#9d407e',
                    500 => '#6b2f5f',
                    600 => '#5a2147',
                    700 => '#463351',
                    800 => '#2e1633',
                    900 => '#200f24',
                    950 => '#170f1a',
                ],
                'secondary' => [
                    50 => '#fdf4f7',
                    100 => '#fce7f3',
                    200 => '#fbcfe8',
                    300 => '#f472b6',
                    400 => '#e28aab',
                    500 => '#b32c5e',
                    600 => '#93254e',
                    700 => '#761e3e',
                    800 => '#5e1731',
                    900 => '#491024',
                    950 => '#2c0714',
                ],
                'gray' => [
                    50 => '#fbfaf7',
                    100 => '#f5f2ed',
                    200 => '#ede8e4',
                    300 => '#e5deda',
                    400 => '#dcd5d2',
                    500 => '#85787f',
                    600 => '#7a7077',
                    700 => '#5f565c',
                    800 => '#2e1633',
                    900 => '#231826',
                    950 => '#170f1a',
                ],
                'success' => [
                    50 => '#fdf4f7',
                    100 => '#fce7f3',
                    200 => '#fbcfe8',
                    300 => '#f472b6',
                    400 => '#e28aab',
                    500 => '#b32c5e',
                    600 => '#93254e',
                    700 => '#761e3e',
                    800 => '#5e1731',
                    900 => '#491024',
                    950 => '#2c0714',
                ],
                'danger' => [
                    50 => '#fdf2f2',
                    100 => '#fde8e8',
                    200 => '#fbd5d5',
                    300 => '#f8b4b4',
                    400 => '#f98080',
                    500 => '#e05046',
                    600 => '#c0392b',
                    700 => '#9b2c20',
                    800 => '#7a2219',
                    900 => '#5c1a13',
                    950 => '#3a100b',
                ],
                'warning' => [
                    50 => '#fffbeb',
                    100 => '#fef3c7',
                    200 => '#fde68a',
                    300 => '#fcd34d',
                    400 => '#fbbf24',
                    500 => '#d4901a',
                    600 => '#d4821a',
                    700 => '#b45309',
                    800 => '#92400e',
                    900 => '#78350f',
                    950 => '#451a03',
                ],
                'info' => [
                    50 => '#eff6ff',
                    100 => '#dbeafe',
                    200 => '#bfdbfe',
                    300 => '#93c5fd',
                    400 => '#4a8be0',
                    500 => '#3a7bd5',
                    600 => '#2563eb',
                    700 => '#1d4ed8',
                    800 => '#1e40af',
                    900 => '#1e3a8a',
                    950 => '#172554',
                ],
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

                        /* --- KCODE OFFICIAL DESIGN SYSTEM COLOR PALETTE --- */
                        :root {
                            --kcode-primary: #2E1633;
                            --kcode-primary-hover: #463351;
                            --kcode-primary-pressed: #200F24;
                            --kcode-secondary: #6B2F5F;
                            --kcode-secondary-pressed: #E0D6DC;
                            --kcode-canvas: #F5F2ED;
                            --kcode-surface: #FBFAF7;
                            --kcode-surface-warm: #EDE8E4;
                            --kcode-surface-deep: #E5DEDA;
                            --kcode-text-primary: #2E1633;
                            --kcode-text-secondary: #5F565C;
                            --text-muted: #7A7077;
                            --kcode-berry: #93254E;
                            --kcode-berry-light: #E28AAB;
                            --kcode-border: #DCD5D2;
                            --kcode-border-control: #85787F;
                            --cta-bg: #2E1633;
                            --cta-hover: #463351;
                            --focus-ring: 2px solid #93254E;
                            --success: #93254E;
                            --error: #C0392B;
                            --warning: #D4821A;
                            --info: #3A7BD5;
                            --navbar-bg: rgba(245, 242, 237, 0.94);
                            --navbar-border: rgba(46, 22, 51, 0.08);
                            --bg-overlay: rgba(46, 22, 51, 0.55);
                        }

                        html.dark {
                            --kcode-primary: #5A2147;
                            --kcode-primary-hover: #732A5B;
                            --kcode-primary-pressed: #441735;
                            --kcode-secondary: #9D407E;
                            --kcode-secondary-pressed: #4A1E3C;
                            --kcode-canvas: #170F1A;
                            --kcode-surface: #231826;
                            --kcode-surface-warm: #2D1F31;
                            --kcode-surface-deep: #38273E;
                            --kcode-text-primary: #F5F0F3;
                            --kcode-text-secondary: #C2B5BF;
                            --text-muted: #9A8A97;
                            --kcode-on-primary: #FFFFFF;
                            --kcode-on-dark: #FAF7F5;
                            --kcode-berry-light: #E28AAB;
                            --kcode-berry: #B32C5E;
                            --kcode-border: #3D2A43;
                            --kcode-border-control: #756377;
                            --cta-bg: #B32C5E;
                            --cta-hover: #E28AAB;
                            --cta-text: #FFFFFF;
                            --focus-ring: 2px solid #E28AAB;
                            --success: #E28AAB;
                            --error: #E05046;
                            --warning: #D4901A;
                            --info: #4A8BE0;
                            --navbar-bg: rgba(23, 15, 26, 0.94);
                            --navbar-border: rgba(255, 255, 255, 0.08);
                            --bg-overlay: rgba(0, 0, 0, 0.75);
                        }

                        /* LIGHT MODE OVERRIDES */
                        html:not(.dark) .fi-layout,
                        html:not(.dark) body {
                            background-color: var(--kcode-canvas) !important;
                            color: var(--kcode-text-primary) !important;
                        }
                        html:not(.dark) .fi-sidebar {
                            background-color: var(--kcode-surface) !important;
                            border-left-color: var(--kcode-border) !important;
                            border-right-color: var(--kcode-border) !important;
                        }
                        html:not(.dark) .fi-topbar {
                            background-color: var(--navbar-bg) !important;
                            border-bottom: 1px solid var(--navbar-border) !important;
                            backdrop-filter: blur(8px);
                        }
                        html:not(.dark) .fi-wi-stats-overview-stat-card,
                        html:not(.dark) .fi-wi-widget,
                        html:not(.dark) .fi-section,
                        html:not(.dark) .fi-ta-content {
                            background-color: var(--kcode-surface) !important;
                            border: 1px solid var(--kcode-border) !important;
                            border-radius: 1.25rem !important;
                            box-shadow: 0 6px 20px -3px rgba(46, 22, 51, 0.06) !important;
                        }
                        html:not(.dark) .fi-wi-stats-overview-stat-card:hover {
                            border-color: var(--kcode-berry) !important;
                            box-shadow: 0 14px 28px -6px rgba(147, 37, 78, 0.18), 0 0 15px rgba(147, 37, 78, 0.1) !important;
                        }

                        /* DARK MODE OVERRIDES */
                        html.dark .fi-layout,
                        html.dark body {
                            background-color: var(--kcode-canvas) !important;
                            color: var(--kcode-text-primary) !important;
                        }
                        html.dark .fi-sidebar {
                            background-color: var(--kcode-surface) !important;
                            border-left-color: var(--kcode-border) !important;
                            border-right-color: var(--kcode-border) !important;
                        }
                        html.dark .fi-topbar {
                            background-color: var(--navbar-bg) !important;
                            border-bottom: 1px solid var(--navbar-border) !important;
                            backdrop-filter: blur(8px);
                        }
                        html.dark .fi-wi-stats-overview-stat-card,
                        html.dark .fi-wi-widget,
                        html.dark .fi-section,
                        html.dark .fi-ta-content {
                            background-color: var(--kcode-surface) !important;
                            border: 1px solid var(--kcode-border) !important;
                            border-radius: 1.25rem !important;
                            box-shadow: 0 8px 25px -4px rgba(0, 0, 0, 0.4) !important;
                        }
                        html.dark .fi-wi-stats-overview-stat-card:hover {
                            border-color: var(--kcode-berry-light) !important;
                            box-shadow: 0 14px 28px -6px rgba(226, 138, 171, 0.22), 0 0 20px rgba(226, 138, 171, 0.15) !important;
                        }

                        /* INPUT CONTROLS & BORDERS */
                        html:not(.dark) input, html:not(.dark) select, html:not(.dark) textarea {
                            border-color: var(--kcode-border-control) !important;
                        }
                        html.dark input, html.dark select, html.dark textarea {
                            border-color: var(--kcode-border-control) !important;
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
                            background-color: var(--kcode-surface-warm) !important;
                        }
                        html.dark .fi-ta-row:hover {
                            background-color: var(--kcode-surface-warm) !important;
                        }

                        /* Buttons Interactions & Full Pill Styling */
                        .fi-btn {
                            border-radius: 9999px !important;
                            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
                        }
                        .fi-btn-primary {
                            background-color: var(--cta-bg) !important;
                            color: #ffffff !important;
                        }
                        .fi-btn-primary:hover {
                            background-color: var(--cta-hover) !important;
                            transform: translateY(-2px) !important;
                            box-shadow: 0 6px 16px -4px rgba(147, 37, 78, 0.35) !important;
                        }
                        .fi-btn:active {
                            transform: translateY(0) scale(0.97) !important;
                        }

                        /* Focus Ring for Accessibility */
                        *:focus-visible {
                            outline: var(--focus-ring) !important;
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
                            filter: drop-shadow(0 4px 12px rgba(147, 37, 78, 0.4)) !important;
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
