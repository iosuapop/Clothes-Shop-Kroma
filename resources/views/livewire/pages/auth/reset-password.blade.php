<?php

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.guest', ['title' => 'New password — KROMA'])] class extends Component
{
    public string $token = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function mount(string $token): void
    {
        $this->token = $token;
        $this->email = request()->query('email', '');
    }

    public function reset(): void
    {
        $this->validate([
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
        ]);

        $status = Password::reset(
            [
                'email' => $this->email,
                'password' => $this->password,
                'password_confirmation' => $this->password_confirmation,
                'token' => $this->token,
            ],
            function ($user) {
                $user->forceFill(['password' => Hash::make($this->password)])->save();
                event(new PasswordReset($user));
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            $this->addError('email', __($status));

            return;
        }

        $this->redirect(route('login'), navigate: true);
    }
}; ?>

<div>
    <h1 class="font-display text-2xl mb-6 text-center">NEW PASSWORD</h1>

    <form wire:submit="reset" class="space-y-4">
        <div>
            <label class="font-mono text-xs">EMAIL</label>
            <input type="email" wire:model="email" class="w-full border-2 border-ink p-2 mt-1">
            @error('email') <p class="text-flash-coral text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="font-mono text-xs">NEW PASSWORD</label>
            <input type="password" wire:model="password" class="w-full border-2 border-ink p-2 mt-1">
            @error('password') <p class="text-flash-coral text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="font-mono text-xs">CONFIRM PASSWORD</label>
            <input type="password" wire:model="password_confirmation" class="w-full border-2 border-ink p-2 mt-1">
        </div>

        <button type="submit" class="card-sticker bg-ink text-bone font-display w-full py-3">
            RESET PASSWORD
        </button>
    </form>
</div>
