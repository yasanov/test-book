<?php

return [
    'adminEmail' => 'admin@example.com',
    'senderEmail' => 'noreply@example.com',
    'senderName' => 'Example.com mailer',
    // SMS configuration (smspilot.ru)
    'sms' => [
        'apiKey' => $_ENV['SMS_API_KEY'] ?? getenv('SMS_API_KEY') ?: '',
        'apiUrl' => $_ENV['SMS_API_URL'] ?? getenv('SMS_API_URL') ?: 'https://smspilot.ru/api.php',
        //'isEmulator' => ($_ENV['SMS_IS_EMULATOR'] ?? getenv('SMS_IS_EMULATOR')) === '1' || ($_ENV['SMS_API_KEY'] ?? getenv('SMS_API_KEY')) === 'XXXXXXXXXXXXYYYYYYYYYYYYZZZZZZZZXXXXXXXXXXXXYYYYYYYYYYYYZZZZZZZZ1234',
    ],
];
