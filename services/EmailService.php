<?php

declare(strict_types=1);

namespace app\services;

use app\exceptions\ServiceException;
use Yii;

class EmailService
{
    public function sendEmail(string $email, string $subject, string $message): bool
    {
        if (empty($email)) {
            throw new ServiceException('Email адрес не указан');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new ServiceException('Некорректный email адрес: ' . $email);
        }

        try {
            $mailer = Yii::$app->mailer;
            $senderEmail = Yii::$app->params['senderEmail'] ?? 'noreply@example.com';
            $senderName = Yii::$app->params['senderName'] ?? 'Books Catalog';

            $result = $mailer->compose()
                ->setTo($email)
                ->setFrom([$senderEmail => $senderName])
                ->setSubject($subject)
                ->setTextBody($message)
                ->send();

            if ($result) {
                Yii::info("Email успешно отправлен на {$email}", 'email');
                return true;
            }

            throw new ServiceException('Не удалось отправить email');
        } catch (ServiceException $e) {
            throw $e;
        } catch (\Exception $e) {
            Yii::error('Ошибка отправки email: ' . $e->getMessage(), 'email');
            throw new ServiceException('Ошибка отправки email: ' . $e->getMessage());
        }
    }
}
