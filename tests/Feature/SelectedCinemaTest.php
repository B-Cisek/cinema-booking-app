<?php

namespace Tests\Feature;

use App\Exceptions\InvalidCinemaException;
use App\Http\Controllers\SelectCinemaController;
use App\Models\Cinema;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;

class SelectedCinemaTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_stores_the_selected_cinema_id_in_the_session(): void
    {
        $cinema = Cinema::query()->create([
            'city' => 'Warszawa',
            'street' => 'ul. Zlota 11',
        ]);

        $response = $this->post(route('cinemas.select'), [
            'id' => $cinema->getKey(),
        ]);

        $response
            ->assertRedirect(route('home'))
            ->assertSessionHas(
                SelectCinemaController::CINEMA_KEY,
                $cinema->getKey(),
            );
    }

    #[Test]
    public function it_throw_exception_when_cinema_is_not_exist(): void
    {
        $this->withoutExceptionHandling();

        $this->assertThrows(
            fn () => $this->post(route('cinemas.select'), [
                'id' => Uuid::uuid7()->toString(),
            ]),
            fn (InvalidCinemaException $exception) => $exception->getMessage() === 'Invalid cinema',
        );
    }
}
