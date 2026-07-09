<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::middleware('guest')->group(function () {
    Volt::route('/login', 'pages.auth.login')->name('login');
    Volt::route('/register', 'pages.auth.register')->name('register');
    Volt::route('/forgot-password', 'pages.auth.forgot-password')->name('password.request');
    Volt::route('/reset-password/{token}', 'pages.auth.reset-password')->name('password.reset');
});

// Logout has no interesting state to manage, so it stays a plain
// controller-free route instead of a Volt/Livewire component.
Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect()->route('home');
})->middleware('auth')->name('logout');
