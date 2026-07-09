<?php

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.guest', ['title' => 'Reset password — KROMA'])] class extends Component
{
    public string $email = '';

    public string $status = '';

    public function sendResetLink(): void
    {
        $this->validate(['email' => 'required|email']);

        $result = Password::sendResetLink(['email' => $this->email]);

        // Same message whether the email exists or not — prevents using
        // this form to check which emails are registered.
        $this->status = 'If that email is registered, a reset link is on its way.';
    }
}; ?>

<div>
    <h1 class="font-display text-2xl mb-6 text-center">RESET PASSWORD</h1>

    @if ($status)
        <p class="font-mono text-xs bg-riot-yellow border-2 border-ink p-3 mb-4">{{ $status }}</p>
    @else
        <form wire:submit="sendResetLink" class="space-y-4">
            <div>
                <label class="font-mono text-xs">EMAIL</label>
                <input type="email" wire:model="email" class="w-full border-2 border-ink p-2 mt-1">
                @error('email') <p class="text-flash-coral text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <button type="submit" class="card-sticker bg-ink text-bone font-display w-full py-3">
                SEND RESET LINK
            </button>
        </form>
    @endif

    <p class="font-mono text-xs text-center mt-6">
        <a href="{{ route('login') }}" class="underline">Back to log in</a>
    </p>
</div>
