<?php

declare(strict_types=1);

namespace app\commands;

use app\models\User;
use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\rbac\DbManager;

class UserController extends Controller
{
    public function actionCreate(string $username, string $password, string $email = ''): int
    {
        if (empty($email)) {
            $email = $username . '@example.com';
        }

        if (User::findByUsername($username) !== null) {
            $this->stdout("Пользователь '{$username}' уже существует.\n", \yii\helpers\Console::FG_RED);
            return ExitCode::DATAERR;
        }

        if (User::findByEmail($email) !== null) {
            $this->stdout("Пользователь с email '{$email}' уже существует.\n", \yii\helpers\Console::FG_RED);
            return ExitCode::DATAERR;
        }

        $user = new User();
        $user->username = $username;
        $user->email = $email;
        $user->setPassword($password);

        if ($user->save()) {
            $this->assignUserRole($user);

            $this->stdout("Пользователь '{$username}' успешно создан.\n", \yii\helpers\Console::FG_GREEN);
            $this->stdout("Email: {$email}\n");
            $this->stdout("ID: {$user->id}\n");
            $this->stdout("Роль 'user' присвоена.\n", \yii\helpers\Console::FG_GREEN);
            return ExitCode::OK;
        }

        $this->stdout("Ошибка при создании пользователя:\n", \yii\helpers\Console::FG_RED);
        foreach ($user->getErrors() as $attribute => $errors) {
            foreach ($errors as $error) {
                $this->stdout("  - {$attribute}: {$error}\n", \yii\helpers\Console::FG_RED);
            }
        }

        return ExitCode::DATAERR;
    }

    public function actionAssignRole(string $username, string $roleName): int
    {
        $user = User::findByUsername($username);
        if ($user === null) {
            $this->stdout("Пользователь '{$username}' не найден.\n", \yii\helpers\Console::FG_RED);
            return ExitCode::DATAERR;
        }

        /** @var DbManager $auth */
        $auth = Yii::$app->authManager;
        $role = $auth->getRole($roleName);

        if ($role === null) {
            $this->stdout("Роль '{$roleName}' не найдена.\n", \yii\helpers\Console::FG_RED);
            return ExitCode::DATAERR;
        }

        if ($auth->getAssignment($roleName, (string)$user->id)) {
            $this->stdout("Пользователю '{$username}' уже присвоена роль '{$roleName}'.\n", \yii\helpers\Console::FG_YELLOW);
            return ExitCode::OK;
        }

        $auth->assign($role, $user->id);
        $this->stdout("Роль '{$roleName}' успешно присвоена пользователю '{$username}'.\n", \yii\helpers\Console::FG_GREEN);

        return ExitCode::OK;
    }

    private function assignUserRole(User $user): void
    {
        /** @var DbManager $auth */
        $auth = Yii::$app->authManager;
        $userRole = $auth->getRole('user');

        if ($userRole && !$auth->getAssignment('user', (string)$user->id)) {
            $auth->assign($userRole, $user->id);
        }
    }
}
