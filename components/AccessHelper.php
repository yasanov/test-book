<?php

declare(strict_types=1);

namespace app\components;

use Yii;
use yii\filters\AccessControl;
use yii\filters\AccessRule;

/**
 * Helper class for AccessControl configuration
 * Упрощает настройку AccessControl в контроллерах
 */
class AccessHelper
{
    /**
     * Creates AccessControl behavior for guest-only actions
     *
     * @param array $actions
     * @return array
     */
    public static function guestOnly(array $actions): array
    {
        return [
            'class' => AccessControl::class,
            'only' => $actions,
            'rules' => [
                [
                    'actions' => $actions,
                    'allow' => true,
                    'roles' => ['?'],
                ],
            ],
        ];
    }

    /**
     * Creates AccessControl behavior for authenticated users only
     *
     * @param array $actions
     * @return array
     */
    public static function userOnly(array $actions): array
    {
        return [
            'class' => AccessControl::class,
            'only' => $actions,
            'rules' => [
                [
                    'actions' => $actions,
                    'allow' => true,
                    'roles' => ['@'],
                ],
            ],
        ];
    }

    /**
     * Creates AccessControl behavior with RBAC permissions
     *
     * @param array $actionsPermissions Массив ['action' => 'permission']
     * @return array
     */
    public static function withPermissions(array $actionsPermissions): array
    {
        $rules = [];
        foreach ($actionsPermissions as $action => $permission) {
            $rules[] = [
                'actions' => [$action],
                'allow' => true,
                'roles' => [$permission],
            ];
        }

        return [
            'class' => AccessControl::class,
            'only' => array_keys($actionsPermissions),
            'rules' => $rules,
        ];
    }

    /**
     * Creates AccessControl behavior for CRUD operations
     *
     * @param string $entityName Название сущности (book, author)
     * @param array $actions Действия ['index', 'view', 'create', 'update', 'delete']
     * @return array
     */
    public static function crudAccess(string $entityName, array $actions = ['index', 'view', 'create', 'update', 'delete']): array
    {
        $rules = [];
        $permissions = [];

        // Просмотр доступен всем
        if (in_array('index', $actions, true) || in_array('view', $actions, true) || in_array('list', $actions, true)) {
            $viewActions = array_intersect(['index', 'view', 'list'], $actions);
            if (!empty($viewActions)) {
                $rules[] = [
                    'actions' => $viewActions,
                    'allow' => true,
                    'roles' => ['view' . ucfirst($entityName)],
                ];
            }
        }

        // CRUD операции только для user
        $crudActions = array_intersect(['create', 'update', 'delete'], $actions);
        if (!empty($crudActions)) {
            foreach ($crudActions as $action) {
                $permission = $action . ucfirst($entityName);
                $rules[] = [
                    'actions' => [$action],
                    'allow' => true,
                    'roles' => [$permission],
                ];
            }
        }

        return [
            'class' => AccessControl::class,
            'only' => $actions,
            'rules' => $rules,
        ];
    }
}
