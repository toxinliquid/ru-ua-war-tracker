<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Visit;

class LogVisitorCountry
{
    public function handle(Request $request, Closure $next)
    {
        $ip = $request->ip();

        // Skip local requests
        if ($ip !== "127.0.0.1") {
            $location = geoip($ip);

            Visit::create([
                'ip'      => $ip,
                'country' => $location->country ?? 'Unknown',
            ]);
        }

        return $next($request);
    }
}
