<?php

return [
    'paths' => ['api/*', 'storage/*', '*'], // السماح بجميع المسارات
    'allowed_methods' => ['*'], // السماح بجميع طرق الطلب (GET, POST, ...)
    'allowed_origins' => ['https://admin.wemarketglobal.com', 'https://wemarketglobal.com'],
    'allowed_headers' => ['*'], // السماح بجميع الهيدرات
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => false, // إذا كنت تحتاج للكوكيز، اجعلها true
];
