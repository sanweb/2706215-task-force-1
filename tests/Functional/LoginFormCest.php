<?php

declare(strict_types=1);

namespace app\tests\Functional;

use app\models\User;
use app\tests\fixtures\UserFixture;
use app\tests\Support\FunctionalTester;

final class LoginFormCest
{
    public function _fixtures(): array
    {
        return [
            'users' => UserFixture::class,
        ];
    }

    public function _before(FunctionalTester $I): void
    {
        $I->amOnRoute('site/login');
    }

    public function openLoginPage(FunctionalTester $I): void
    {
        $I->see('Login', 'h1');
    }

    // demonstrates `amLoggedInAs` method
    public function internalLoginById(FunctionalTester $I): void
    {
        $I->amLoggedInAs(100);
        $I->amOnPage('/');
        $I->see('Logout (Admin)');
    }

    // demonstrates `amLoggedInAs` method
    public function internalLoginByInstance(FunctionalTester $I): void
    {
        $I->amLoggedInAs(User::findByEmail('admin@example.com'));
        $I->amOnPage('/');
        $I->see('Logout (Admin)');
    }

    public function loginWithEmptyCredentials(FunctionalTester $I): void
    {
        $I->submitForm('#login-form', []);
        $I->expectTo('see validation errors');
        $I->see('Email cannot be blank.');
        $I->see('Password cannot be blank.');
    }

    public function loginWithWrongCredentials(FunctionalTester $I): void
    {
        $I->submitForm('#login-form', [
            'LoginForm[email]' => 'admin@example.com',
            'LoginForm[password]' => 'wrong',
        ]);
        $I->expectTo('see validation errors');
        $I->see('Incorrect email or password.');
    }

    public function loginSuccessfully(FunctionalTester $I): void
    {
        $I->submitForm('#login-form', [
            'LoginForm[email]' => 'admin@example.com',
            'LoginForm[password]' => 'admin',
        ]);
        $I->see('Logout (Admin)');
        $I->dontSeeElement('form#login-form');
    }
}
