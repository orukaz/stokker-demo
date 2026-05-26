<?php

namespace App\Http\Middleware;

use App\Models\ProductFavorite;
use App\Support\ProductFavoriteOwner;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
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
        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'auth' => [
                'user' => $request->user(),
            ],
            'favoritesCount' => fn () => $this->favoritesCount($request),
            'sidebarOpen' => $request->cookie('sidebar_state', 'true') === 'true',
        ];
    }

    private function favoritesCount(Request $request): int
    {
        $owner = ProductFavoriteOwner::attributes($request);

        if ($owner === []) {
            return 0;
        }

        return ProductFavorite::query()
            ->where($owner)
            ->count();
    }
}
