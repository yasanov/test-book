<?php

declare(strict_types=1);

namespace app\commands;

use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\rbac\DbManager;

/**
 * RBAC initialization controller
 * Инициализация ролей и разрешений для RBAC
 */
class RbacController extends Controller
{
    /**
     * Инициализация RBAC: создание ролей и разрешений
     *
     * @return int
     */
    public function actionInit(): int
    {
        /** @var DbManager $auth */
        $auth = Yii::$app->authManager;

        // Удаляем старые данные (если есть)
        $auth->removeAll();

        // Создаем разрешения (permissions)

        // Разрешения для книг
        $viewBook = $auth->createPermission('viewBook');
        $viewBook->description = 'Просмотр книг';
        $auth->add($viewBook);

        $createBook = $auth->createPermission('createBook');
        $createBook->description = 'Создание книг';
        $auth->add($createBook);

        $updateBook = $auth->createPermission('updateBook');
        $updateBook->description = 'Редактирование книг';
        $auth->add($updateBook);

        $deleteBook = $auth->createPermission('deleteBook');
        $deleteBook->description = 'Удаление книг';
        $auth->add($deleteBook);

        // Разрешения для авторов
        $viewAuthor = $auth->createPermission('viewAuthor');
        $viewAuthor->description = 'Просмотр авторов';
        $auth->add($viewAuthor);

        $createAuthor = $auth->createPermission('createAuthor');
        $createAuthor->description = 'Создание авторов';
        $auth->add($createAuthor);

        $updateAuthor = $auth->createPermission('updateAuthor');
        $updateAuthor->description = 'Редактирование авторов';
        $auth->add($updateAuthor);

        $deleteAuthor = $auth->createPermission('deleteAuthor');
        $deleteAuthor->description = 'Удаление авторов';
        $auth->add($deleteAuthor);

        // Разрешение для подписки (только для гостей)
        $subscribeToAuthor = $auth->createPermission('subscribeToAuthor');
        $subscribeToAuthor->description = 'Подписка на автора';
        $auth->add($subscribeToAuthor);

        // Разрешение для просмотра отчета
        $viewReport = $auth->createPermission('viewReport');
        $viewReport->description = 'Просмотр отчета ТОП-10 авторов';
        $auth->add($viewReport);

        // Создаем роли

        // Роль "Гость" (guest) - по умолчанию для всех неавторизованных пользователей
        $guest = $auth->createRole('guest');
        $guest->description = 'Гость (неавторизованный пользователь)';
        $auth->add($guest);

        // Гость может: просматривать книги, авторов, подписываться на авторов, смотреть отчеты
        $auth->addChild($guest, $viewBook);
        $auth->addChild($guest, $viewAuthor);
        $auth->addChild($guest, $subscribeToAuthor);
        $auth->addChild($guest, $viewReport);

        // Роль "Пользователь" (user) - для авторизованных пользователей
        $user = $auth->createRole('user');
        $user->description = 'Пользователь (авторизованный)';
        $auth->add($user);

        // Пользователь наследует все права гостя
        $auth->addChild($user, $guest);

        // Пользователь может: создавать, редактировать и удалять книги и авторов
        $auth->addChild($user, $createBook);
        $auth->addChild($user, $updateBook);
        $auth->addChild($user, $deleteBook);
        $auth->addChild($user, $createAuthor);
        $auth->addChild($user, $updateAuthor);
        $auth->addChild($user, $deleteAuthor);

        $this->stdout("RBAC успешно инициализирован!\n", \yii\helpers\Console::FG_GREEN);
        $this->stdout("Созданы роли: guest, user\n");
        $this->stdout("Созданы разрешения для CRUD операций\n");

        return ExitCode::OK;
    }
}
