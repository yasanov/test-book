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

    /**
     * @param int $authorId
     * @param array $data
     * @return AuthorSubscription
     * @throws NotFoundException
     * @throws ServiceException
     */
    public function subscribe(int $authorId, array $data): AuthorSubscription
    {
        $author = $this->authorRepository->findById($authorId);
        if ($author === null) {
            throw new NotFoundException('Автор не найден.');
        }

        $subscription = new AuthorSubscription();
        $subscription->author_id = $authorId;
        $subscription->load($data);

        if (!$this->subscriptionRepository->save($subscription)) {
            throw new ServiceException('Не удалось создать подписку.');
        }

        return $subscription;
    }
}
