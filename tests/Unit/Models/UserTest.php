<?php

declare(strict_types=1);

namespace app\tests\Unit\Models;

use app\models\User;
use app\tests\fixtures\UserFixture;
use \Codeception\Test\Unit;

final class UserTest extends Unit
{
    public function _fixtures(): array
    {
        return [
            'users' => UserFixture::class,
        ];
    }

    public function testFindUserById(): void
    {
        /** @var User $user */
        $user = User::findIdentity(100);

        verify($user)->notNull();
        verify($user->id)->equals(100);
        verify(User::findIdentity(999))->null();
    }

    public function testFindUserByAccessToken(): void
    {
        /** @var User $user */
        $user = User::findIdentityByAccessToken('100-token');

        verify($user)->notEmpty();
        verify($user->username)->equals('admin');
        verify(User::findIdentityByAccessToken('non-existing'))->empty();
    }

    public function testFindUserByEmail(): void
    {
        $user = User::findByEmail('admin@example.com');

        verify($user)->notNull();
        verify($user->email)->equals('admin@example.com');

        verify(User::findByEmail('not-admin@example.com'))->null();
    }

    public function testValidateUser(): void
    {
        /** @var User $user */
        $user = User::findByEmail('admin@example.com');

        //TODO: Add AuthKey support
        verify($user->validateAuthKey('test100key'))->notEmpty();
        verify($user->validateAuthKey('test102key'))->empty();
    }
}
