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

    /**
     * @return array the validation rules.
     */
    public function rules(): array
    {
        return [
            // username and password are both required
            [['username', 'password'], 'required'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword(string $attribute, array $params = []): void
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();

            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Incorrect username or password.');
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     * @return bool whether the user is logged in successfully
     */
    public function login(): bool
    {
        if ($this->validate()) {
            $user = $this->getUser();
            if ($user && Yii::$app->user->login($user, $this->rememberMe ? 3600 * 24 * 30 : 0)) {
                // Назначаем роль user при первом входе, если еще не назначена
                $this->assignUserRole($user);
                return true;
            }
        }
        return false;
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    public function getUser(): ?User
    {
        if ($this->_user === null) {
            $this->_user = User::findByUsername($this->username);
        }

        return $this->_user;
    }

    /**
     * Assigns 'user' role to the user if not already assigned
     *
     * @param User $user
     */
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
