<?php

declare(strict_types=1);

namespace app\services\notifications;

use app\exceptions\ServiceException;
use app\models\AuthorSubscription;
use app\services\EmailService;
use Yii;

class EmailNotificationStrategy implements NotificationStrategyInterface
{
    public function __construct(
        private readonly EmailService $emailService
    ) {
    }

    public function canSend(AuthorSubscription $subscription): bool
    {
        $email = trim($subscription->email ?? '');

        return !empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    public function send(AuthorSubscription $subscription, string $subject, string $message): void
    {
        $email = trim($subscription->email ?? '');
        
        if (empty($email)) {
            return;
        }

        try {
            $this->emailService->sendEmail($email, $subject, $message);
        } catch (ServiceException $e) {
            Yii::error(
                "Ошибка отправки Email подписчику {$email} (ID: {$subscription->id}): " . $e->getMessage(),
                'email'
            );
        }
    }
}
