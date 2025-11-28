<?php

$isProduction = env('APP_ENV') === 'production';

$defaultPublicDomain = $isProduction ? 'kleros.app' : 'kleros.local';
$defaultAdminDomain = $isProduction ? 'admin.kleros.app' : 'admin.local';

return [
    'public' => env('APP_DOMAIN_PUBLIC', $defaultPublicDomain),
    'admin' => env('APP_DOMAIN_ADMIN', $defaultAdminDomain),
    'scheme' => env('APP_DOMAIN_SCHEME', $isProduction ? 'https' : 'http'),
];
