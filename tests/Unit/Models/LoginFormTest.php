<?php

declare(strict_types=1);

namespace app\tests\Unit\Models;

use app\models\LoginForm;
use app\tests\fixtures\UserFixture;
use Codeception\Test\Unit;
use Yii;
use yii\base\Security;

final class LoginFormTest extends Unit
{
    private $_model;

    public function _fixtures(): array
    {
        return [
            'users' => UserFixture::class,
        ];
    }

    protected function _after(): void
    {
        Yii::$app->user->logout();
    }

    public function testLoginNoUser(): void
    {
        $this->_model = new LoginForm(
            new Security(),
            [
                'email' => 'not-existing@example.com',
                'password' => 'not_existing_password',
            ],
        );

        verify($this->_model->login())->false();
        verify(Yii::$app->user->isGuest)->true();
    }

    public function testLoginWrongPassword(): void
    {
        $this->_model = new LoginForm(
            new Security(),
            [
                'email' => 'demo@example.com',
                'password' => 'wrong_password',
            ],
        );

        verify($this->_model->login())->false();
        verify(Yii::$app->user->isGuest)->true();
        verify($this->_model->errors)->arrayHasKey('password');
    }

    public function testLoginCorrect(): void
    {
        $this->_model = new LoginForm(
            new Security(),
            [
                'email' => 'demo@example.com',
                'password' => 'demo',
            ],
        );

        verify($this->_model->login())->true();
        verify(Yii::$app->user->isGuest)->false();
        verify($this->_model->errors)->arrayHasNotKey('password');
    }
}
