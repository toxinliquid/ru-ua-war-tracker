<?php

return [

    'log_failures' => true,

    'include_currency' => true,

    /*
    |--------------------------------------------------------------------------
    | Default Service
    |--------------------------------------------------------------------------
    */
    'service' => env('GEOIP_DRIVER', 'ipapi'),

    /*
    |--------------------------------------------------------------------------
    | Services
    |--------------------------------------------------------------------------
    */
    'services' => [

        'ipapi' => [
            'class' => \Torann\GeoIP\Services\IPApi::class,
            'secure' => true,
            'lang'   => null,
        ],

        'maxmind_database' => [
            'class' => \Torann\GeoIP\Services\MaxMindDatabase::class,
            'database_path' => storage_path('app/geoip.mmdb'),
            'update_url' => sprintf(
                'https://download.maxmind.com/app/geoip_download?edition_id=GeoLite2-City&license_key=%s&suffix=tar.gz',
                env('MAXMIND_LICENSE_KEY')
            ),
            'locales' => ['en'],
        ],

        'maxmind_api' => [
            'class' => \Torann\GeoIP\Services\MaxMindWebService::class,
            'user_id' => env('MAXMIND_USER_ID'),
            'license_key' => env('MAXMIND_LICENSE_KEY'),
            'locales' => ['en'],
        ],

        'ipgeolocation' => [
            'class' => \Torann\GeoIP\Services\IPGeoLocation::class,
            'secure' => true,
            'key' => env('IPGEOLOCATION_KEY'),
            'continent_path' => storage_path('app/continents.json'),
            'lang' => 'en',
        ],

        'ipdata' => [
            'class' => \Torann\GeoIP\Services\IPData::class,
            'key' => env('IPDATA_API_KEY'),
            'secure' => true,
        ],

        'ipfinder' => [
            'class' => \Torann\GeoIP\Services\IPFinder::class,
            'key' => env('IPFINDER_API_KEY'),
            'secure' => true,
            'locales' => ['en'],
        ],
    ],

    'cache' => 'all',

    'cache_tags' => ['torann-geoip-location'],

    'cache_expires' => 30,

    'default_location' => [
        'ip' => '127.0.0.0',
        'iso_code' => 'US',
        'country' => 'United States',
        'city' => 'New Haven',
        'state' => 'CT',
        'state_name' => 'Connecticut',
        'postal_code' => '06510',
        'lat' => 41.31,
        'lon' => -72.92,
        'timezone' => 'America/New_York',
        'continent' => 'NA',
        'default' => true,
        'currency' => 'USD',
    ],

];
