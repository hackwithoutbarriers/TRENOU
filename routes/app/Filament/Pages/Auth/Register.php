<?php

namespace App\Filament\Pages\Auth;

use App\Models\User;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Form;
use Filament\Http\Responses\Auth\Contracts\RegistrationResponse;
use Filament\Notifications\Notification;
use Filament\Pages\Auth\Register as BaseRegister;
use Illuminate\Validation\Rule;

class Register extends BaseRegister
{
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                $this->getNameFormComponent(),
                $this->getEmailFormComponent()
                    ->rules([
                        'email',
                        Rule::unique('users', 'email'),
                    ]),
                $this->getPasswordFormComponent(),
                $this->getPasswordConfirmationFormComponent(),
                Placeholder::make('security_notice')
                    ->label('Protection renforcée')
                    ->content('Votre candidature est envoyée au super administrateur pour validation. Aucun accès ne sera accordé avant approbation.'),
            ]);
    }

    public function register(): ?RegistrationResponse
    {
        $data = $this->form->getState();

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'status' => User::STATUS_PENDING,
            'is_superuser' => false,
        ]);

        Notification::make()
            ->success()
            ->title('Demande envoyée')
            ->body('Votre inscription a bien été enregistrée. Elle est en attente de validation par le super administrateur.')
            ->send();

        $this->redirect(filament()->getLoginUrl(), navigate: true);

        return null;
    }

    public function getTitle(): string
    {
        return 'Créer un compte artisan';
    }

    public function getHeading(): string
    {
        return 'Créer votre accès sécurisé';
    }

    protected function mutateFormDataBeforeRegister(array $data): array
    {
        $data['status'] = User::STATUS_PENDING;
        $data['is_superuser'] = false;
        $data['two_factor_secret'] = null;
        $data['two_factor_recovery_codes'] = null;
        $data['two_factor_confirmed_at'] = null;

        return $data;
    }
}
