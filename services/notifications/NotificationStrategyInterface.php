<?php

declare(strict_types=1);

namespace app\services\notifications;

use app\models\AuthorSubscription;

interface NotificationStrategyInterface
{
    public function canSend(AuthorSubscription $subscription): bool;

    public function send(AuthorSubscription $subscription, string $subject, string $message): void;
}
