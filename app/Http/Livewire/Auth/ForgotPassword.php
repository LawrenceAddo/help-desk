<?php

namespace App\Http\Livewire\Auth;

use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class ForgotPassword extends Component implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    public string $email = '';

    public function mount(): void
    {
        $this->form->fill([
            'email' => '',
        ]);
    }

    protected function getFormSchema(): array
    {
        return [
            TextInput::make('email')
                ->label('Email address')
                ->email()
                ->required()
                ->placeholder('Email address')
                ->exists('users', 'email'),
        ];
    }

    public function submit(): void
    {
        $data = $this->form->getState();

        $status = Password::sendResetLink(
            ['email' => $data['email']]
        );

        if ($status === Password::RESET_LINK_SENT) {
            Notification::make()
                ->title(__('A password reset link has been sent to your email address.'))
                ->success()
                ->send();
        } else {
            throw ValidationException::withMessages([
                'email' => [__($status)],
            ]);
        }
    }

    public function render()
    {
        return view('livewire.auth.forgot-password');
    }
}