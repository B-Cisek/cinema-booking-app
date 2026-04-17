<?php

declare(strict_types=1);

namespace Tests\Feature\Support\Identity;

use App\Support\Identity\GuestTokenManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;

class GuestTokenManagerTest extends TestCase
{
    #[Test]
    public function it_returns_the_existing_guest_token_from_the_request_cookie(): void
    {
        $existingToken = Uuid::uuid7()->toString();
        $request = Request::create('/', 'GET', cookies: [
            'guest-token' => $existingToken,
        ]);

        $token = (new GuestTokenManager)->resolve($request);

        $this->assertSame($existingToken, $token);
    }

    #[Test]
    public function it_generates_a_uuid_when_the_request_does_not_have_a_guest_token_cookie(): void
    {
        Cookie::shouldReceive('queue')->once();

        $token = (new GuestTokenManager)->resolve(Request::create('/'));

        $this->assertTrue(Uuid::isValid($token));
    }

    #[Test]
    public function it_queues_a_guest_token_cookie_with_a_numeric_lifetime_when_the_cookie_is_missing(): void
    {
        Cookie::shouldReceive('queue')
            ->once()
            ->with(
                'guest-token',
                Mockery::on(static fn (mixed $value): bool => is_string($value) && Uuid::isValid($value)),
                60 * 24 * 30,
            );

        (new GuestTokenManager)->setup(Request::create('/'));

        $this->addToAssertionCount(1);
    }

    #[Test]
    public function it_does_not_queue_a_guest_token_cookie_when_the_request_already_has_one(): void
    {
        $existingToken = Uuid::uuid7()->toString();

        Cookie::shouldReceive('queue')->never();

        $token = (new GuestTokenManager)->setup(Request::create('/', 'GET', cookies: [
            'guest-token' => $existingToken,
        ]));

        $this->assertSame($existingToken, $token);
    }

    #[Test]
    public function it_returns_the_same_token_when_resolved_multiple_times_in_the_same_request(): void
    {
        Cookie::shouldReceive('queue')->once();

        $request = Request::create('/');
        $handler = new GuestTokenManager;

        $firstToken = $handler->resolve($request);
        $secondToken = $handler->resolve($request);

        $this->assertSame($firstToken, $secondToken);
    }

    #[Test]
    public function it_rotates_an_invalid_guest_token_cookie(): void
    {
        Cookie::shouldReceive('queue')
            ->once()
            ->with(
                'guest-token',
                Mockery::on(static fn (mixed $value): bool => is_string($value) && Uuid::isValid($value)),
                60 * 24 * 30,
            );

        $token = (new GuestTokenManager)->resolve(Request::create('/', 'GET', cookies: [
            'guest-token' => 'invalid-token',
        ]));

        $this->assertTrue(Uuid::isValid($token));
    }
}
