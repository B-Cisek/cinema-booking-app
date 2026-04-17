<?php

declare(strict_types=1);

namespace Tests\Feature\Support\Identity;

use App\Commands\SelectCinema;
use App\Models\Cinema;
use App\Repositories\CinemaRepository;
use App\Support\Identity\CinemaResolver;
use Illuminate\Http\Request;
use Illuminate\Session\ArraySessionHandler;
use Illuminate\Session\Store;
use Illuminate\Support\Facades\Cookie;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CinemaResolverTest extends TestCase
{
    private MockInterface $cinemaRepository;

    private CinemaResolver $resolver;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cinemaRepository = Mockery::mock(CinemaRepository::class);
        $this->resolver = new CinemaResolver($this->cinemaRepository);
    }

    #[Test]
    public function it_returns_the_cached_cinema_from_the_request_attributes(): void
    {
        $cinema = $this->makeCinema('cinema-1');
        $request = $this->makeRequest();
        $request->attributes->set('selected_cinema', $cinema);

        $this->cinemaRepository->shouldNotReceive('getById');

        $this->assertSame($cinema, $this->resolver->resolve($request));
    }

    #[Test]
    public function it_resolves_the_cinema_from_the_session_and_caches_it_in_the_request(): void
    {
        $cinema = $this->makeCinema('cinema-1');
        $request = $this->makeRequest();
        $request->session()->put(SelectCinema::CINEMA_SESSION_KEY, $cinema->getKey());

        $this->cinemaRepository->shouldReceive('getById')
            ->once()
            ->with($cinema->getKey())
            ->andReturn($cinema);

        $this->assertSame($cinema, $this->resolver->resolve($request));
        $this->assertSame($cinema, $request->attributes->get('selected_cinema'));
    }

    #[Test]
    public function it_forgets_an_invalid_session_cinema_and_returns_null_when_cookie_is_missing(): void
    {
        config()->set('app.selected_cinema_cookie_name', 'selected-cinema');

        $request = $this->makeRequest();
        $request->session()->put(SelectCinema::CINEMA_SESSION_KEY, 'missing-cinema');

        $this->cinemaRepository->shouldReceive('getById')
            ->once()
            ->with('missing-cinema')
            ->andReturn(null);

        $this->assertNull($this->resolver->resolve($request));
        $this->assertFalse($request->session()->has(SelectCinema::CINEMA_SESSION_KEY));
        $this->assertNull($request->attributes->get('selected_cinema'));
    }

    #[Test]
    public function it_restores_the_cinema_from_the_cookie_when_the_session_is_missing(): void
    {
        config()->set('app.selected_cinema_cookie_name', 'selected-cinema');

        $cinema = $this->makeCinema('cinema-1');
        $request = $this->makeRequest(cookies: [
            'selected-cinema' => $cinema->getKey(),
        ]);

        $this->cinemaRepository->shouldReceive('getById')
            ->once()
            ->with($cinema->getKey())
            ->andReturn($cinema);

        $this->assertSame($cinema, $this->resolver->resolve($request));
        $this->assertSame($cinema->getKey(), $request->session()->get(SelectCinema::CINEMA_SESSION_KEY));
        $this->assertSame($cinema, $request->attributes->get('selected_cinema'));
    }

    #[Test]
    public function it_expires_the_cookie_when_the_cinema_from_the_cookie_does_not_exist(): void
    {
        config()->set('app.selected_cinema_cookie_name', 'selected-cinema');

        $request = $this->makeRequest(cookies: [
            'selected-cinema' => 'missing-cinema',
        ]);

        $this->cinemaRepository->shouldReceive('getById')
            ->once()
            ->with('missing-cinema')
            ->andReturn(null);

        Cookie::shouldReceive('expire')
            ->once()
            ->with('selected-cinema');

        $this->assertNull($this->resolver->resolve($request));
        $this->assertNull($request->attributes->get('selected_cinema'));
    }

    #[Test]
    public function it_queues_the_cookie_with_the_configured_name_and_lifetime(): void
    {
        config()->set('app.selected_cinema_cookie_name', 'selected-cinema');
        config()->set('app.selected_cinema_cookie_lifetime_minutes', 120);

        Cookie::shouldReceive('queue')
            ->once()
            ->with('selected-cinema', 'cinema-1', 120);

        $this->resolver->queueCookie('cinema-1');

        $this->addToAssertionCount(1);
    }

    /**
     * @param  array<string, string>  $cookies
     */
    private function makeRequest(array $cookies = []): Request
    {
        $request = Request::create(uri: '/', cookies: $cookies);
        $session = new Store('test', new ArraySessionHandler(10));
        $session->start();
        $request->setLaravelSession($session);

        return $request;
    }

    private function makeCinema(string $id): Cinema
    {
        $cinema = new Cinema([
            'city' => 'Warsaw',
            'street' => 'Main Street 1',
        ]);

        $cinema->setAttribute($cinema->getKeyName(), $id);

        return $cinema;
    }
}
