<?php

namespace App\Filament\Pages\Auth;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Auth\EditProfile as BaseEditProfile;
use Illuminate\Validation\ValidationException;
use PragmaRX\Google2FA\Google2FA;

class EditProfile extends BaseEditProfile
{
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                $this->getNameFormComponent(),
                $this->getEmailFormComponent(),
                $this->getPasswordFormComponent(),
                $this->getPasswordConfirmationFormComponent(),
                TextInput::make('two_factor_secret')
                    ->label('Clé 2FA')
                    ->readOnly()
                    ->copyable()
                    ->helperText('Ajoutez cette clé dans votre application d’authentification (Google Authenticator, Microsoft Authenticator, etc.).')
                    ->dehydrated(true)
                    ->default(fn (): string => $this->getUser()->two_factor_secret ?? $this->generateTwoFactorSecret()),
                TextInput::make('two_factor_code')
                    ->label('Code de vérification')
                    ->numeric()
                    ->length(6)
                    ->prefixIcon('heroicon-o-shield-check')
                    ->helperText('Saisissez le code affiché par votre application pour confirmer l’activation du 2FA.')
                    ->dehydrated(false)
                    ->visible(fn (): bool => filled($this->getUser()->two_factor_secret) && blank($this->getUser()->two_factor_confirmed_at)),
            ]);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $user = $this->getUser();
        $secret = $data['two_factor_secret'] ?? $user->two_factor_secret ?? null;

        if (filled($secret) && filled($data['two_factor_code'] ?? null)) {
            $code = trim((string) $data['two_factor_code']);
            $google2fa = app(Google2FA::class);

            if (! $google2fa->verifyKey($secret, $code, 8)) {
                throw ValidationException::withMessages([
                    'data.two_factor_code' => 'Le code de vérification 2FA est invalide.',
                ]);
            }

            $data['two_factor_secret'] = $secret;
            $data['two_factor_confirmed_at'] = now();
        }

        return $data;
    }

    protected function generateTwoFactorSecret(): string
    {
        $user = $this->getUser();

        if (blank($user->two_factor_secret)) {
            $user->forceFill([
                'two_factor_secret' => app(Google2FA::class)->generateSecretKey(),
            ]);

            $user->saveQuietly();
        }

        return $user->fresh()->two_factor_secret;
    }
}
