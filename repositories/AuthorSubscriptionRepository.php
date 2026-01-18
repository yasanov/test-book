<?php

declare(strict_types=1);

namespace app\repositories;

use app\models\AuthorSubscription;

class AuthorSubscriptionRepository
{
    public function save(AuthorSubscription $subscription): bool
    {
        return $subscription->save();
    }

    public function exists(int $authorId, string $email, string $phone): bool
    {
        $query = AuthorSubscription::find()
            ->where(['author_id' => $authorId]);

        $conditions = ['or'];
        
        if (!empty($email)) {
            $conditions[] = ['email' => $email];
        }
        
        if (!empty($phone)) {
            $conditions[] = ['phone' => $phone];
        }

        if (count($conditions) === 1) {
            return false;
        }

        return $query->andWhere($conditions)->exists();
    }

    public function findByAuthorIdBatch(int $authorId, int $batchSize = 100): \Generator
    {
        $query = AuthorSubscription::find()
            ->where(['author_id' => $authorId]);

        foreach ($query->batch($batchSize) as $subscriptions) {
            yield $subscriptions;
        }
    }
}
