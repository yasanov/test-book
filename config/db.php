<?php

/**
 * Database configuration
 * Автоматически определяет окружение: Docker или локальное
 */
$isDocker = ($_ENV['DOCKER_ENV'] ?? getenv('DOCKER_ENV')) || (isset($_SERVER['DOCKER_ENV']) && $_SERVER['DOCKER_ENV']);

return [
    'class' => 'yii\db\Connection',
    'dsn' => $isDocker 
        ? 'mysql:host=mysql;dbname=books_catalog' 
        : 'mysql:host=localhost;dbname=books_catalog',
    'username' => $isDocker ? 'catalog' : 'root',
    'password' => $isDocker ? 'catalog' : '',
    'charset' => 'utf8mb4',

    // Schema cache options (for production environment)
    //'enableSchemaCache' => true,
    //'schemaCacheDuration' => 60,
    //'schemaCache' => 'cache',
];
