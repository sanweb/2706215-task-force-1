<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "executor_profile".
 *
 * @property int $id
 * @property int $user_id
 * @property string|null $phone
 * @property string|null $telegram
 * @property string|null $about
 * @property int $hide_my_contacts
 * @property string $created_at
 * @property string|null $updated_at
 *
 * @property User $user
 */
class ExecutorProfile extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'executor_profile';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['phone', 'telegram', 'about', 'updated_at'], 'default', 'value' => null],
            [['hide_my_contacts'], 'default', 'value' => 0],
            [['user_id'], 'required'],
            [['user_id', 'hide_my_contacts'], 'integer'],
            [['about'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['phone'], 'string', 'max' => 20],
            [['telegram'], 'string', 'max' => 64],
            [['user_id'], 'unique'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'phone' => 'Phone',
            'telegram' => 'Telegram',
            'about' => 'About',
            'hide_my_contacts' => 'Hide My Contacts',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

}
