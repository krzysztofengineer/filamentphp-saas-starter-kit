<?php

namespace App\Http\Controllers\Pwa;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class ManifestController extends Controller
{
    public function __invoke(): JsonResponse
    {
        return response()->json([
            'name' => config('app.name'),
            'short_name' => config('app.name'),
            'description' => 'A reusable SAAS template.',
            'start_url' => '/app',
            'scope' => '/',
            'display' => 'standalone',
            'orientation' => 'portrait',
            'theme_color' => '#2563EB',
            'background_color' => '#FDFDFC',
            'lang' => 'en',
            'dir' => 'ltr',
            'icons' => [
                [
                    'src' => '/icons/icon-192.png',
                    'sizes' => '192x192',
                    'type' => 'image/png',
                    'purpose' => 'any',
                ],
                [
                    'src' => '/icons/icon-512.png',
                    'sizes' => '512x512',
                    'type' => 'image/png',
                    'purpose' => 'any',
                ],
                [
                    'src' => '/icons/icon-512.png',
                    'sizes' => '512x512',
                    'type' => 'image/png',
                    'purpose' => 'maskable',
                ],
            ],
        ], 200, [
            'Content-Type' => 'application/manifest+json',
        ]);
    }
}
