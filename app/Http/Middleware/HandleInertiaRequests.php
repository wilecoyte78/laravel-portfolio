<?php

namespace App\Http\Middleware;

use App\Models\NavigationItem;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    /**
     * Routes that skip server-side rendering. The admin area is behind a
     * login (nothing to index) and uses browser-only APIs.
     *
     * @var array<int, string>
     */
    protected $withoutSsr = [
        'admin/*',
        'login',
    ];

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $request->user(),
            ],
            'navigation' => fn () => NavigationItem::tree(),
            'seo' => [
                // Current URL without the query string; used for canonical and og:url.
                'url' => fn () => $request->url(),
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
        ]);
    }
}
