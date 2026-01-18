<?php

declare(strict_types=1);

namespace app\models;

use Yii;
use yii\base\Model;
use yii\rbac\DbManager;

/**
 * LoginForm is the model behind the login form.
 *
 * @property-read User|null $user
 */
class LoginForm extends Model
{
    public string $username = '';
    public string $password = '';
    public bool $rememberMe = true;

    private ?User $_user = null;

    public function rules(): array
    {
        return [
            [['username', 'password'], 'required'],
            ['rememberMe', 'boolean'],
            ['password', 'validatePassword'],
        ];
    }

    public function validatePassword(string $attribute): void
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();

            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Incorrect username or password.');
            }
        }
    }

    public function login(): bool
    {
        if ($this->validate()) {
            $user = $this->getUser();
            if ($user && Yii::$app->user->login($user, $this->rememberMe ? 3600 * 24 * 30 : 0)) {
                $this->assignUserRole($user);
                return true;
            }
        }
        return false;
    }

    public function getUser(): ?User
    {
        if ($this->_user === null) {
            $this->_user = User::findByUsername($this->username);
        }

        return $this->_user;
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
