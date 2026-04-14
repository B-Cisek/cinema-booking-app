<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Attributes\UseTranslations;
use App\Services\TranslationPropResolver;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class TranslationPropResolverTest extends TestCase
{
    private Translator $translator;

    private TranslationPropResolver $resolver;

    protected function setUp(): void
    {
        parent::setUp();

        $this->translator = $this->createMock(Translator::class);
        $this->resolver = new TranslationPropResolver($this->translator);
    }

    #[Test]
    public function it_returns_translations_for_an_invokable_controller_with_the_attribute(): void
    {
        $this->translator->expects($this->once())
            ->method('get')
            ->with('home')
            ->willReturn([
                'select_cinema_message' => 'Please select a cinema.',
            ]);

        $request = $this->makeRequestReturningAction(
            TranslationPropResolverTestInvokableController::class,
        );

        $this->assertSame([
            'select_cinema_message' => 'Please select a cinema.',
        ], $this->resolver->resolve($request));
    }

    #[Test]
    public function it_returns_translations_for_a_named_controller_action_with_the_attribute(): void
    {
        $this->translator->expects($this->once())
            ->method('get')
            ->with('home')
            ->willReturn([
                'select_cinema_message' => 'Proszę wybrać kino.',
            ]);

        $request = $this->makeRequestReturningAction(
            TranslationPropResolverTestController::class.'@index',
        );

        $this->assertSame([
            'select_cinema_message' => 'Proszę wybrać kino.',
        ], $this->resolver->resolve($request));
    }

    #[Test]
    public function it_returns_an_empty_array_when_the_controller_action_has_no_translation_attribute(): void
    {
        $this->translator->expects($this->never())
            ->method('get');

        $request = $this->makeRequestReturningAction(
            TranslationPropResolverTestController::class.'@show',
        );

        $this->assertSame([], $this->resolver->resolve($request));
    }

    #[Test]
    public function it_returns_an_empty_array_when_the_request_has_no_route(): void
    {
        $request = Request::create('/');

        $this->translator->expects($this->never())
            ->method('get');

        $this->assertSame([], $this->resolver->resolve($request));
    }

    #[Test]
    public function it_returns_an_empty_array_when_the_action_is_not_a_string(): void
    {
        $request = Request::create('/');
        $route = new Route(['GET'], '/', [
            'uses' => static fn (): null => null,
        ]);

        $request->setRouteResolver(static fn (): Route => $route);

        $this->translator->expects($this->never())
            ->method('get');

        $this->assertSame([], $this->resolver->resolve($request));
    }

    private function makeRequestReturningAction(string $action): Request
    {
        $request = Request::create('/');
        $route = new Route(['GET'], '/', [
            'uses' => $action,
            'controller' => $action,
        ]);

        $request->setRouteResolver(static fn (): Route => $route);

        return $request;
    }
}

final class TranslationPropResolverTestInvokableController
{
    #[UseTranslations(key: 'home')]
    public function __invoke(): void {}
}

final class TranslationPropResolverTestController
{
    #[UseTranslations(key: 'home')]
    public function index(): void {}

    public function show(): void {}
}
