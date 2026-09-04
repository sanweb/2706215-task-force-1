<?php

declare(strict_types=1);

namespace app\requests;

use app\models\City;
use app\models\User;
use yii\base\Model;

class UserSignupRequest extends Model
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $passwordConfirm = '';
    public string|int|null $cityId = null;
    public string|int|bool $isExecutor = false;

    public function rules(): array
    {
        return [
            [['name', 'email', 'cityId', 'password', 'passwordConfirm'], 'required'],

            ['name', 'string', 'min' => 2, 'max' => 128],

            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            [
                'email',
                'unique',
                'targetClass' => User::class,
                'targetAttribute' => 'email',
                'message' => 'Пользователь с таким Email уже зарегистрирован.',
            ],

            ['cityId', 'integer'],
            [
                'cityId',
                'exist',
                'targetClass' => City::class,
                'targetAttribute' => ['cityId' => 'id'],
            ],

            ['password', 'string', 'min' => 8],

            [
                'passwordConfirm',
                'compare',
                'compareAttribute' => 'password',
                'message' => 'Пароли не совпадают.',
            ],

            ['isExecutor', 'boolean'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'name' => 'Ваше имя',
            'email' => 'Email',
            'cityId' => 'Город',
            'password' => 'Пароль',
            'passwordConfirm' => 'Повтор пароля',
            'isExecutor' => 'Я собираюсь откликаться на заказы',
        ];
    }

    public function formName(): string
    {
        return 'signup';
    }
}
