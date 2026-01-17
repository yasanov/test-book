<?php

declare(strict_types=1);

namespace app\repositories;

use app\models\AuthorSubscription;

class AuthorSubscriptionRepository
{
    /**
     * @param AuthorSubscription $subscription
     * @return bool
     */
    public function save(AuthorSubscription $subscription): bool
    {
        return $subscription->save();
    }

    /**
     * @param int $authorId
     * @param string $email
     * @param string $phone
     * @return bool
     */
    public function exists(int $authorId, string $email, string $phone): bool
    {
        return AuthorSubscription::find()
            ->where(['author_id' => $authorId, 'email' => $email, 'phone' => $phone])
            ->exists();
    }
}
