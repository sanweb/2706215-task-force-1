<?php

declare(strict_types=1);

namespace app\models;

use Yii;
use yii\base\Model;
use yii\base\Security;

/**
 * LoginForm is the model behind the login form.
 *
 * @property-read User|null $user
 *
 */
class LoginForm extends Model
{
    public string $email = '';
    public string $password = '';
    public bool $rememberMe = true;

    private ?User $_user = null;
    private bool $_userLoaded = false;

    public function __construct(
        private readonly Security $security,
        $config = []
    ) {
        parent::__construct($config);
    }

    /**
     * Returns the validation rules.
     */
    public function rules(): array
    {
        return [
            [['email', 'password'], 'required'],
            ['email', 'email'],
            ['rememberMe', 'boolean'],
            ['password', 'validatePassword'],
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array|null $params the additional name-value pairs given in the rule
     */
    public function validatePassword(string $attribute, ?array $params): void
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();

            if (
                $user === null
                || $user->password === null
                || !$this->security->validatePassword($this->password, $user->password)
            ) {
                $this->addError($attribute, 'Incorrect email or password.');
            }
        }
    }

    /**
     * Logs in a user using the provided email and password.
     */
    public function login(): bool
    {
        if ($this->validate()) {
            return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600 * 24 * 30 : 0);
        }

        return false;
    }

    /**
     * Finds user by email.
     */
    public function getUser(): ?User
    {
        if (!$this->_userLoaded) {
            $this->_user = User::findByEmail($this->email);
            $this->_userLoaded = true;
        }

        return $this->_user;
    }
}
