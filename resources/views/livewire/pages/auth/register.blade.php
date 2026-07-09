<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.guest', ['title' => 'Create account — KROMA'])] class extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function register(): void
    {
        $validated = $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        Auth::login($user);

        $this->redirect(route('home'), navigate: true);
    }
}; ?>

<div>
    <h1 class="font-display text-2xl mb-6 text-center">JOIN THE DROP</h1>

    <form wire:submit="register" class="space-y-4">
        <div>
            <label class="font-mono text-xs">NAME</label>
            <input type="text" wire:model="name" class="w-full border-2 border-ink p-2 mt-1">
            @error('name') <p class="text-flash-coral text-xs mt-1">{{ $message }}</p> @enderror
        </div>

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

        <div>
            <label class="font-mono text-xs">CONFIRM PASSWORD</label>
            <input type="password" wire:model="password_confirmation" class="w-full border-2 border-ink p-2 mt-1">
        </div>

        <button type="submit" class="card-sticker bg-ink text-bone font-display w-full py-3">
            CREATE ACCOUNT
        </button>
    </form>

    <p class="font-mono text-xs text-center mt-6">
        Already have an account? <a href="{{ route('login') }}" class="underline">Log in</a>
    </p>
</div>
