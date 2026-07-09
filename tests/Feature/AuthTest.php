<?php

use App\Models\User;
use Livewire\Volt\Volt;

it('registers a new user and logs them in', function () {
    Volt::test('pages.auth.register')
        ->set('name', 'Jane Doe')
        ->set('email', 'jane@example.com')
        ->set('password', 'password123')
        ->set('password_confirmation', 'password123')
        ->call('register');

    $this->assertAuthenticated();
    expect(User::where('email', 'jane@example.com')->exists())->toBeTrue();
});

it('rejects registration when passwords do not match', function () {
    Volt::test('pages.auth.register')
        ->set('name', 'Jane Doe')
        ->set('email', 'jane@example.com')
        ->set('password', 'password123')
        ->set('password_confirmation', 'somethingelse')
        ->call('register')
        ->assertHasErrors('password');

    $this->assertGuest();
});

it('logs in with correct credentials', function () {
    $user = User::factory()->create(['email' => 'known@example.com', 'password' => bcrypt('secret123')]);

    $this->withSession([]);

    Volt::test('pages.auth.login')
        ->set('email', 'known@example.com')
        ->set('password', 'secret123')
        ->call('login');

    $this->assertAuthenticatedAs($user);
});

it('rejects login with the wrong password', function () {
    User::factory()->create(['email' => 'known@example.com', 'password' => bcrypt('secret123')]);

    Volt::test('pages.auth.login')
        ->set('email', 'known@example.com')
        ->set('password', 'wrong-password')
        ->call('login')
        ->assertHasErrors('email');

    $this->assertGuest();
});
