<?php

declare(strict_types=1);

namespace app\services;

use app\exceptions\NotFoundException;
use app\exceptions\ServiceException;
use app\models\AuthorSubscription;
use app\repositories\AuthorRepository;
use app\repositories\AuthorSubscriptionRepository;

class SubscriptionService
{
    public function __construct(
        private readonly AuthorRepository $authorRepository,
        private readonly AuthorSubscriptionRepository $subscriptionRepository
    ) {
    }

    public function subscribe(int $authorId, array $data): AuthorSubscription
    {
        $author = $this->authorRepository->findById($authorId);
        if ($author === null) {
            throw new NotFoundException('Автор не найден.');
        }

        $subscription = new AuthorSubscription();
        $subscription->author_id = $authorId;
        $subscription->load($data);

        $email = trim($subscription->email ?? '');
        $phone = trim($subscription->phone ?? '');
        if ($email || $phone) {
            if ($this->subscriptionRepository->exists($authorId, $email, $phone)) {
                throw new ServiceException('Вы уже подписаны на этого автора с данным email и/или телефоном.');
            }
        }

        if (!$subscription->validate()) {
            $errors = $subscription->getFirstErrors();
            $errorMessage = !empty($errors) ? reset($errors) : 'Ошибка валидации данных подписки.';
            throw new ServiceException($errorMessage);
        }

        if (!$this->subscriptionRepository->save($subscription)) {
            $errors = $subscription->getFirstErrors();
            $errorMessage = !empty($errors) ? reset($errors) : 'Не удалось создать подписку.';
            throw new ServiceException($errorMessage);
        }

        return $subscription;
    }
}
