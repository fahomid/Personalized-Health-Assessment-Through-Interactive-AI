<?php

namespace App\Http\Middleware;

use Closure;
use GeoIp2\Database\Reader;

class GeoIPMiddleware {
    public function handle($request, Closure $next) {

        // getting user's IP address
        $ip = $request->ip();

        // setting path to GeoIP2 database file
        $reader = new Reader(storage_path('app/GeoLite2-Country.mmdb')); // Path to your GeoIP2 database file

        try {

            # get country based on IP address
            $record = $reader->country($ip);

            // attach the user's country
            $request->merge(['country' => $record->country->name]);

            # attach the user's country code
            $request->merge(['country_code' => $record->country->isoCode]);
        } catch (\Exception $e) {

            // attach the user's country to anonymous
            $request->merge(['country' => "Anonymous"]);

            # attach the user's country code to ANON
            $request->merge(['country_code' => "ANON"]);
        }

        return $next($request);
    }
}
