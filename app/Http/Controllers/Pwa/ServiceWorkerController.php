<?php

namespace App\Http\Controllers\Pwa;

use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\Response;

class ServiceWorkerController extends Controller
{
    public function __invoke(): Response
    {
        $path = public_path('sw.js');

        abort_unless(is_file($path), 404);

        $version = substr(md5_file($path), 0, 10);
        $body = str_replace('__SW_VERSION__', $version, file_get_contents($path));

        return response($body, 200, [
            'Content-Type' => 'application/javascript',
            'Service-Worker-Allowed' => '/',
            'Cache-Control' => 'no-cache',
        ]);
    }
}
