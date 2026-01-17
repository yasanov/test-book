<?php

declare(strict_types=1);

namespace app\components;

use app\repositories\AuthorRepository;
use app\repositories\AuthorSubscriptionRepository;
use app\repositories\BookAuthorRepository;
use app\repositories\BookRepository;
use app\repositories\ReportRepository;
use app\services\AuthorService;
use app\services\BookService;
use app\services\ReportService;
use app\services\SubscriptionService;
use Yii;

/**
 * Bootstrap component for Dependency Injection configuration
 */
class DIBootstrap implements \yii\base\BootstrapInterface
{
    /**
     * @param \yii\base\Application $app
     * @return void
     */
    public function bootstrap($app): void
    {
        // Register repositories as singletons
        Yii::$container->setSingletons([
            BookRepository::class => BookRepository::class,
            AuthorRepository::class => AuthorRepository::class,
            AuthorSubscriptionRepository::class => AuthorSubscriptionRepository::class,
            BookAuthorRepository::class => BookAuthorRepository::class,
            ReportRepository::class => ReportRepository::class,
        ]);

        // Register services with dependencies
        Yii::$container->setDefinitions([
            BookService::class => function ($container, $params, $config) {
                return new BookService(
                    $container->get(BookRepository::class),
                    $container->get(AuthorRepository::class),
                    $container->get(BookAuthorRepository::class)
                );
            },
            AuthorService::class => function ($container, $params, $config) {
                return new AuthorService(
                    $container->get(AuthorRepository::class)
                );
            },
            SubscriptionService::class => function ($container, $params, $config) {
                return new SubscriptionService(
                    $container->get(AuthorRepository::class),
                    $container->get(AuthorSubscriptionRepository::class)
                );
            },
            ReportService::class => function ($container, $params, $config) {
                return new ReportService(
                    $container->get(ReportRepository::class)
                );
            },
        ]);
    }
}
