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

                        /* --- VIBRANT MULTI-COLOR SIDEBAR ICONS --- */
                        .fi-sidebar-item-icon {
                            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
                            padding: 2px !important;
                            border-radius: 6px !important;
                        }
                        .fi-sidebar-item:hover .fi-sidebar-item-icon {
                            transform: scale(1.25) rotate(4deg) !important;
                        }

                        /* 1. Sales & Orders -> Emerald Green Glow */
                        .fi-sidebar-item[href*="admin/orders"] .fi-sidebar-item-icon,
                        .fi-sidebar-item-button[href*="admin/orders"] svg {
                            color: #10b981 !important;
                            filter: drop-shadow(0 2px 8px rgba(16, 185, 129, 0.5)) !important;
                        }

                        /* 2. Coupons & Offers -> Golden Amber Glow */
                        .fi-sidebar-item[href*="admin/coupons"] .fi-sidebar-item-icon,
                        .fi-sidebar-item[href*="admin/offers"] .fi-sidebar-item-icon,
                        .fi-sidebar-item-button[href*="admin/coupons"] svg,
                        .fi-sidebar-item-button[href*="admin/offers"] svg {
                            color: #f59e0b !important;
                            filter: drop-shadow(0 2px 8px rgba(245, 158, 11, 0.5)) !important;
                        }

                        /* 3. Products & Catalog -> Purple Violet Glow */
                        .fi-sidebar-item[href*="admin/products"] .fi-sidebar-item-icon,
                        .fi-sidebar-item[href*="admin/categories"] .fi-sidebar-item-icon,
                        .fi-sidebar-item[href*="admin/sub-categories"] .fi-sidebar-item-icon,
                        .fi-sidebar-item[href*="admin/brands"] .fi-sidebar-item-icon,
                        .fi-sidebar-item-button[href*="admin/products"] svg,
                        .fi-sidebar-item-button[href*="admin/categories"] svg,
                        .fi-sidebar-item-button[href*="admin/sub-categories"] svg,
                        .fi-sidebar-item-button[href*="admin/brands"] svg {
                            color: #8b5cf6 !important;
                            filter: drop-shadow(0 2px 8px rgba(139, 92, 246, 0.5)) !important;
                        }

                        /* 4. Customers & Roles -> Electric Blue Glow */
                        .fi-sidebar-item[href*="admin/users"] .fi-sidebar-item-icon,
                        .fi-sidebar-item[href*="admin/roles"] .fi-sidebar-item-icon,
                        .fi-sidebar-item-button[href*="admin/users"] svg,
                        .fi-sidebar-item-button[href*="admin/roles"] svg {
                            color: #06b6d4 !important;
                            filter: drop-shadow(0 2px 8px rgba(6, 182, 212, 0.5)) !important;
                        }

                        /* 5. Notifications -> Rose Pink / Red Glow */
                        .fi-sidebar-item[href*="admin/app-notifications"] .fi-sidebar-item-icon,
                        .fi-sidebar-item-button[href*="admin/app-notifications"] svg {
                            color: #f43f5e !important;
                            filter: drop-shadow(0 2px 8px rgba(244, 63, 94, 0.5)) !important;
                        }

                        /* 6. AI Chatbot -> Indigo Purple Glow */
                        .fi-sidebar-item[href*="admin/chatbot-messages"] .fi-sidebar-item-icon,
                        .fi-sidebar-item[href*="admin/chatbot-suggestions"] .fi-sidebar-item-icon,
                        .fi-sidebar-item-button[href*="admin/chatbot-messages"] svg,
                        .fi-sidebar-item-button[href*="admin/chatbot-suggestions"] svg {
                            color: #6366f1 !important;
                            filter: drop-shadow(0 2px 8px rgba(99, 102, 241, 0.5)) !important;
                        }

                        /* 7. Skincare & Quiz -> Bright Mint Teal Glow */
                        .fi-sidebar-item[href*="admin/assessments"] .fi-sidebar-item-icon,
                        .fi-sidebar-item[href*="admin/concerns"] .fi-sidebar-item-icon,
                        .fi-sidebar-item[href*="admin/quiz-questions"] .fi-sidebar-item-icon,
                        .fi-sidebar-item[href*="admin/skin-types"] .fi-sidebar-item-icon,
                        .fi-sidebar-item-button[href*="admin/assessments"] svg,
                        .fi-sidebar-item-button[href*="admin/concerns"] svg,
                        .fi-sidebar-item-button[href*="admin/quiz-questions"] svg,
                        .fi-sidebar-item-button[href*="admin/skin-types"] svg {
                            color: #14b8a6 !important;
                            filter: drop-shadow(0 2px 8px rgba(20, 184, 166, 0.5)) !important;
                        }

                        /* 8. Blog & Articles -> Warm Orange Glow */
                        .fi-sidebar-item[href*="admin/blogs"] .fi-sidebar-item-icon,
                        .fi-sidebar-item[href*="admin/blog-categories"] .fi-sidebar-item-icon,
                        .fi-sidebar-item[href*="admin/blog-tags"] .fi-sidebar-item-icon,
                        .fi-sidebar-item-button[href*="admin/blogs"] svg,
                        .fi-sidebar-item-button[href*="admin/blog-categories"] svg,
                        .fi-sidebar-item-button[href*="admin/blog-tags"] svg {
                            color: #f97316 !important;
                            filter: drop-shadow(0 2px 8px rgba(249, 115, 22, 0.5)) !important;
                        }

                        /* 9. Newsletter, FAQs, Pages -> Sky Blue Glow */
                        .fi-sidebar-item[href*="admin/newsletter-subscriptions"] .fi-sidebar-item-icon,
                        .fi-sidebar-item[href*="admin/faqs"] .fi-sidebar-item-icon,
                        .fi-sidebar-item[href*="admin/pages"] .fi-sidebar-item-icon,
                        .fi-sidebar-item-button[href*="admin/newsletter-subscriptions"] svg,
                        .fi-sidebar-item-button[href*="admin/faqs"] svg,
                        .fi-sidebar-item-button[href*="admin/pages"] svg {
                            color: #0284c7 !important;
                            filter: drop-shadow(0 2px 8px rgba(2, 132, 199, 0.5)) !important;
                        }

                        /* 10. Loyalty, Activity Logs, Settings -> Gold / Amber Glow */
                        .fi-sidebar-item[href*="admin/loyalty-levels"] .fi-sidebar-item-icon,
                        .fi-sidebar-item[href*="admin/activity-logs"] .fi-sidebar-item-icon,
                        .fi-sidebar-item[href*="admin/settings"] .fi-sidebar-item-icon,
                        .fi-sidebar-item-button[href*="admin/loyalty-levels"] svg,
                        .fi-sidebar-item-button[href*="admin/activity-logs"] svg,
                        .fi-sidebar-item-button[href*="admin/settings"] svg {
                            color: #eab308 !important;
                            filter: drop-shadow(0 2px 8px rgba(234, 179, 8, 0.5)) !important;
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
