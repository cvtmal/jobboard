<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Illuminate\Foundation\Inspiring;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Inertia\Middleware;
use Tighten\Ziggy\Ziggy;

final class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        [$message, $author] = str(Inspiring::quotes()->random())->explode('-'); // @phpstan-ignore-line

        return [ // @phpstan-ignore-line
            ...parent::share($request),
            'name' => config('app.name'),
            'quote' => ['message' => mb_trim($message), 'author' => mb_trim($author)], // @phpstan-ignore-line
            'auth' => [
                'user' => $request->user(),
                'company' => $request->user('company'),
            ],
            'locale' => [
                'current' => App::currentLocale(),
                'available' => ['en', 'de'],
            ],
            'ziggy' => fn (): array => [
                ...(new Ziggy)->toArray(),
                'location' => $request->url(),
            ],
        ];
    }
}
