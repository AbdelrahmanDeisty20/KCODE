<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\EditProfile as BaseEditProfile;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Schemas\Components;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class EditProfile extends BaseEditProfile
{
    public static function getLabel(): string
    {
        return app()->getLocale() === 'en' ? 'Edit Profile' : 'تعديل الحساب والملف الشخصي';
    }

    public function getTitle(): string | Htmlable
    {
        return app()->getLocale() === 'en' ? 'Edit Profile | KCODE' : 'تعديل الحساب | KCODE';
    }

    public function getHeading(): string | Htmlable
    {
        return app()->getLocale() === 'en' ? 'Account & Profile Settings' : 'إعدادات الحساب والملف الشخصي';
    }

    public function getSubheading(): string | Htmlable | null
    {
        return app()->getLocale() === 'en'
            ? 'Manage your personal details, profile picture, contact information, and security credentials.'
            : 'تحديث بياناتك الشخصية، صورتك الرمزية، معلومات الاتصال وكلمة المرور الخاصة بحسابك في لوحة التحكم.';
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Components\Section::make(app()->getLocale() === 'en' ? 'Personal Information' : 'المعلومات الشخصية والاتصال')
                    ->description(app()->getLocale() === 'en' ? 'Update your basic profile information' : 'تعديل الاسم والبريد الإلكتروني ورقم الهاتف والصورة الشخصية')
                    ->schema([
                        Forms\Components\FileUpload::make('image')
                            ->label('الصورة الشخصية (Avatar)')
                            ->image()
                            ->avatar()
                            ->directory('users')
                            ->disk('public')
                            ->columnSpanFull()
                            ->alignCenter(),

                        Forms\Components\TextInput::make('name')
                            ->label('الاسم الكامل')
                            ->required()
                            ->maxLength(255)
                            ->autofocus(),

                        Forms\Components\TextInput::make('email')
                            ->label('البريد الإلكتروني')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),

                        Forms\Components\TextInput::make('phone')
                            ->label('رقم الهاتف')
                            ->tel()
                            ->maxLength(25)
                            ->placeholder('+968 9000 0000'),

                        Forms\Components\TextInput::make('city')
                            ->label('المدينة / المنطقة')
                            ->maxLength(100)
                            ->placeholder('مسقط، صلالة...'),
                    ])->columns(2),

                Components\Section::make(app()->getLocale() === 'en' ? 'Security & Password' : 'الأمان وتغيير كلمة المرور')
                    ->description(app()->getLocale() === 'en' ? 'Update your account password safely' : 'اترك الحقول فارغة إذا كنت لا ترغب بتغيير كلمة المرور الحالية')
                    ->schema([
                        Forms\Components\TextInput::make('currentPassword')
                            ->label('كلمة المرور الحالية')
                            ->password()
                            ->revealable()
                            ->currentPassword(guard: Filament::getAuthGuard())
                            ->visible(fn (Get $get): bool => filled($get('password')) || ($get('email') !== $this->getUser()->getAttributeValue('email')))
                            ->required(fn (Get $get): bool => filled($get('password')))
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('password')
                            ->label('كلمة المرور الجديدة')
                            ->password()
                            ->revealable()
                            ->rule(Password::default())
                            ->autocomplete('new-password')
                            ->dehydrated(fn ($state): bool => filled($state))
                            ->dehydrateStateUsing(fn ($state): string => Hash::make($state))
                            ->live(debounce: 500)
                            ->same('passwordConfirmation'),

                        Forms\Components\TextInput::make('passwordConfirmation')
                            ->label('تأكيد كلمة المرور الجديدة')
                            ->password()
                            ->revealable()
                            ->autocomplete('new-password')
                            ->required(fn (Get $get): bool => filled($get('password')))
                            ->visible(fn (Get $get): bool => filled($get('password')))
                            ->dehydrated(false),
                    ])->columns(2),
            ]);
    }
}

