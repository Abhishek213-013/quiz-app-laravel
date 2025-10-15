<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use Illuminate\Support\Facades\Cache;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): string|null
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $request->user(),
            ],
            'flash' => [
                'message' => fn () => $request->session()->get('message'),
                'type' => fn () => $request->session()->get('type')
            ],
            // FIXED: Share theme globally so all pages can access it
            'customization' => [
                'colorScheme' => Cache::get('theme_preview.color_scheme', Cache::get('customization.color_scheme', 'light')),
                'primaryColor' => Cache::get('theme_preview.primary_color', Cache::get('customization.primary_color', 'blue')),
                'layout' => Cache::get('customization.layout', 'sidebar'),
            ]
        ]);
    }
}