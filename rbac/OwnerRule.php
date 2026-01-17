<?php

declare(strict_types=1);

namespace app\rbac;

use Yii;
use yii\rbac\Rule;

class OwnerRule extends Rule
{
    public $name = 'isOwner';

    /**
     * {@inheritdoc}
     */
    public function execute($user, $item, $params): bool
    {
        return isset($params['model']) && $params['model']->created_by == $user;
    }
}
