<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Events\UserLoggedIn;
use App\Events\UserRegistered;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Inertia\Testing\AssertableInertia as Assert;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_renders_login_and_register_pages_for_guests(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('Login'));

        $this->get(route('register'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('Register'));
    }

    #[Test]
    public function it_registers_a_user_and_authenticates_them(): void
    {
        Event::fake();

        $response = $this->post(route('register.store'), [
            'email' => 'nowy@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect(route('home'));

        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'nowy@example.com',
        ]);

        Event::assertDispatched(
            UserRegistered::class,
            fn (UserRegistered $event): bool => $event->user->email === 'nowy@example.com',
        );
    }

    #[Test]
    public function it_logs_in_an_existing_user(): void
    {
        Event::fake();

        $user = User::factory()->create([
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'password',
            'remember' => true,
        ]);

        $response->assertRedirect(route('home'));
        $this->assertAuthenticatedAs($user);
        Event::assertDispatched(
            UserLoggedIn::class,
            fn (UserLoggedIn $event): bool => $event->user->is($user),
        );
    }

    #[Test]
    public function it_rejects_invalid_login_credentials(): void
    {
        User::factory()->create([
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->from(route('login'))->post(route('login.store'), [
            'email' => 'user@example.com',
            'password' => 'wrong-password',
        ]);

        $response
            ->assertRedirect(route('login'))
            ->assertInvalid([
                'email' => __('auth.failed'),
            ]);

        $this->assertGuest();
    }

    #[Test]
    public function it_logs_out_an_authenticated_user(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('logout'));

        $response->assertRedirect(route('home'));

        $this->assertGuest();
    }
}
