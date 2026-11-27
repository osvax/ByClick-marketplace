<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class RobotsController extends Controller
{
    /**
     * Allow public pages and point crawlers to the XML sitemap.
     */
    public function __invoke(): Response
    {
        $content = implode("\n", [
            'User-agent: *',
            'Allow: /',
            'Disallow: /admin',
            'Disallow: /seller/dashboard',
            'Disallow: /cart',
            'Disallow: /checkout',
            'Disallow: /login',
            'Disallow: /register',
            '',
            'Sitemap: '.url('/sitemap.xml'),
            '',
        ]);

        return response($content, 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }
}
