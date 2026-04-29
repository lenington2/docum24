<?php

namespace App\Http\Controllers;

use App\Models\PageVisit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PageVisitController extends Controller
{
    public static function record(Request $request): void
    {
        $ip = $request->ip();
        $geo = ['country' => null, 'city' => null];

        // Geolocalización gratuita con ip-api.com
        try {
            $response = Http::timeout(3)->get("http://ip-api.com/json/{$ip}?fields=country,city,status");
            if ($response->ok() && $response->json('status') === 'success') {
                $geo['country'] = $response->json('country');
                $geo['city']    = $response->json('city');
            }
        } catch (\Throwable) {}

        PageVisit::create([
            'ip'         => $ip,
            'country'    => $geo['country'],
            'city'       => $geo['city'],
            'user_agent' => $request->userAgent(),
            'referer'    => $request->headers->get('referer'),
        ]);
    }
}