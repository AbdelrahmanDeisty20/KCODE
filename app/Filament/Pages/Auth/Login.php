<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Schemas\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Checkbox;
use Filament\Actions\Action;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;

class Login extends BaseLogin
{
    public function getTitle(): string | Htmlable
    {
        return 'تسجيل الدخول | KCODE';
    }

    public function getHeading(): string | Htmlable
    {
        return new HtmlString('
            <div class="kcode-auth-topbar">
                <div class="kcode-auth-avatar">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                </div>
                <div class="kcode-auth-brand-badge">
                    <span class="kcode-auth-badge-text">حساب</span>
                    <span class="kcode-auth-badge-logo">KCODE</span>
                </div>
            </div>
            <div class="kcode-auth-heading-wrap">
                <h1 class="kcode-auth-title">أهلاً بك في KCODE</h1>
            </div>
        ');
    }

    public function getSubheading(): string | Htmlable | null
    {
        return new HtmlString('
            <div class="kcode-auth-sub-wrap">
                <p class="kcode-auth-subtext">اختر طريقة الدخول أو إنشاء الحساب.</p>
                <div class="kcode-auth-tabs">
                    <button type="button" class="kcode-auth-tab">إنشاء حساب</button>
                    <button type="button" class="kcode-auth-tab is-active">تسجيل الدخول</button>
                </div>
            </div>
        ');
    }

    protected function getEmailFormComponent(): Component
    {
        return TextInput::make('email')
            ->label('البريد الإلكتروني')
            ->email()
            ->required()
            ->autocomplete('email')
            ->autofocus()
            ->placeholder('name@example.com')
            ->extraInputAttributes(['dir' => 'ltr']);
    }

    protected function getPasswordFormComponent(): Component
    {
        return TextInput::make('password')
            ->label('كلمة المرور')
            ->password()
            ->revealable(true)
            ->required()
            ->autocomplete('current-password')
            ->placeholder('••••••••');
    }

    protected function getRememberFormComponent(): Component
    {
        return Checkbox::make('remember')
            ->label('تذكرني');
    }

    protected function getAuthenticateFormAction(): Action
    {
        return Action::make('authenticate')
            ->label('تسجيل الدخول  ←')
            ->submit('authenticate')
            ->extraAttributes([
                'class' => 'kcode-auth-submit-btn',
            ]);
    }

    public function hasLogo(): bool
    {
        return false;
    }
}
