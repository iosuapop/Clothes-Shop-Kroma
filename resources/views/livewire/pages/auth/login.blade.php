<?php

use App\Services\CartService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;

new #[Layout('components.layouts.guest', ['title' => 'Log in — KROMA'])] class extends Component
{
    #[Validate('required|email')]
    public string $email = '';

    #[Validate('required')]
    public string $password = '';

    public bool $remember = false;

    public function login(): void
    {
        $this->validate();

        // Basic brute-force throttling keyed by email+IP, standard
        // Laravel pattern rather than a hand-rolled attempt counter.
        $key = Str::lower($this->email).'|'.request()->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $this->addError('email', 'Too many attempts. Please try again in a minute.');

            return;
        }

        if (! Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            RateLimiter::hit($key, 60);
            $this->addError('email', 'These credentials do not match our records.');

            return;
        }

        RateLimiter::clear($key);
        session()->regenerate();

        // Whatever the guest added to their cart before logging in now
        // belongs to their account — see CartService::mergeGuestCartInto().
        (new CartService(null, session()->getId()))->mergeGuestCartInto(Auth::user());

        $this->redirect(route('home'), navigate: true);
    }
}; ?>

<div>
    <h1 class="font-display text-2xl mb-6 text-center">LOG IN</h1>

    <form wire:submit="login" class="space-y-4">
        <div>
            <label class="font-mono text-xs">EMAIL</label>
            <input type="email" wire:model="email" class="w-full border-2 border-ink p-2 mt-1">
            @error('email') <p class="text-flash-coral text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="font-mono text-xs">PASSWORD</label>
            <input type="password" wire:model="password" class="w-full border-2 border-ink p-2 mt-1">
            @error('password') <p class="text-flash-coral text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <label class="flex items-center gap-2 font-mono text-xs">
            <input type="checkbox" wire:model="remember"> REMEMBER ME
        </label>

        <a href="{{ route('password.request') }}" class="font-mono text-xs underline block">Forgot password?</a>

        <button type="submit" class="card-sticker bg-ink text-bone font-display w-full py-3">
            LOG IN
        </button>
    </form>

    <p class="font-mono text-xs text-center mt-6">
        No account? <a href="{{ route('register') }}" class="underline">Register</a>
    </p>
</div>
