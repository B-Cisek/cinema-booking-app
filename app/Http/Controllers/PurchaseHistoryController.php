<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use App\ViewData\PurchaseHistoryPageData;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Inertia\ResponseFactory;

class PurchaseHistoryController extends Controller
{
    public function __construct(
        private readonly PurchaseHistoryPageData $data,
    ) {}

    public function __invoke(Request $request): Response|ResponseFactory
    {
        /** @var User $user */
        $user = $request->user();

        return Inertia::render('PurchaseHistory', $this->data->build($user));
    }
}
