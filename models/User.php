<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string $email
 * @property string $name
 * @property string|null $password
 * @property int|null $city_id
 * @property string|null $avatar
 * @property string|null $birthday
 * @property int $is_executor
 * @property string $created_at
 * @property string|null $updated_at
 *
 * @property Bid[] $bs
 * @property Category[] $categories
 * @property City $city
 * @property ExecutorProfile $executorProfile
 * @property ExecutorSpecialization[] $executorSpecializations
 * @property Review[] $reviews
 * @property Review[] $reviews0
 * @property Task[] $tasks
 * @property Task[] $tasks0
 * @property Task[] $tasks1
 */
class User extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['password', 'city_id', 'avatar', 'birthday', 'updated_at'], 'default', 'value' => null],
            [['is_executor'], 'default', 'value' => 0],
            [['email', 'name'], 'required'],
            [['city_id', 'is_executor'], 'integer'],
            [['birthday', 'created_at', 'updated_at'], 'safe'],
            [['email', 'password', 'avatar'], 'string', 'max' => 255],
            [['name'], 'string', 'max' => 128],
            [['email'], 'unique'],
            [['city_id'], 'exist', 'skipOnError' => true, 'targetClass' => City::class, 'targetAttribute' => ['city_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'email' => 'Email',
            'name' => 'Name',
            'password' => 'Password',
            'city_id' => 'City ID',
            'avatar' => 'Avatar',
            'birthday' => 'Birthday',
            'is_executor' => 'Is Executor',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[Bs]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBs()
    {
        return $this->hasMany(Bid::class, ['user_id' => 'id']);
    }

    /**
     * Gets query for [[Categories]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCategories()
    {
        return $this->hasMany(Category::class, ['id' => 'category_id'])->viaTable('executor_specialization', ['user_id' => 'id']);
    }

    /**
     * Gets query for [[City]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCity()
    {
        return $this->hasOne(City::class, ['id' => 'city_id']);
    }

    /**
     * Gets query for [[ExecutorProfile]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getExecutorProfile()
    {
        return $this->hasOne(ExecutorProfile::class, ['user_id' => 'id']);
    }

    /**
     * Gets query for [[ExecutorSpecializations]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getExecutorSpecializations()
    {
        return $this->hasMany(ExecutorSpecialization::class, ['user_id' => 'id']);
    }

    /**
     * Gets query for [[Reviews]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getReviews()
    {
        return $this->hasMany(Review::class, ['customer_id' => 'id']);
    }

    /**
     * Gets query for [[Reviews0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getReviews0()
    {
        return $this->hasMany(Review::class, ['executor_id' => 'id']);
    }

    /**
     * Gets query for [[Tasks]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTasks()
    {
        return $this->hasMany(Task::class, ['customer_id' => 'id']);
    }

    /**
     * Gets query for [[Tasks0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTasks0()
    {
        return $this->hasMany(Task::class, ['executor_id' => 'id']);
    }

    /**
     * Gets query for [[Tasks1]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTasks1()
    {
        return $this->hasMany(Task::class, ['id' => 'task_id'])->viaTable('bid', ['user_id' => 'id']);
    }

}
