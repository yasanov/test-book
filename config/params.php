<?php

return [
    'adminEmail' => 'admin@example.com',
    'senderEmail' => 'noreply@example.com',
    'senderName' => 'Example.com mailer',
    // Yandex Object Storage (S3) configuration
    's3' => [
        'endpoint' => getenv('S3_ENDPOINT') ?: 'https://storage.yandexcloud.net',
        'region' => getenv('S3_REGION') ?: 'ru-central1',
        'bucket' => getenv('S3_BUCKET') ?: '',
        'accessKey' => getenv('S3_ACCESS_KEY') ?: '',
        'secretKey' => getenv('S3_SECRET_KEY') ?: '',
        'pathPrefix' => 'books/', // Префикс для файлов книг
    ],
];
