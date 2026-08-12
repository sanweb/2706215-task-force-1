<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "task".
 *
 * @property int $id
 * @property int $customer_id
 * @property int $category_id
 * @property int|null $executor_id
 * @property string $title
 * @property string $description
 * @property int $budget
 * @property string $expire_date
 * @property string $status
 * @property string|null $location
 * @property int|null $city_id
 * @property float|null $lat
 * @property float|null $lng
 * @property string $created_at
 * @property string|null $updated_at
 *
 * @property Attachment[] $attachments
 * @property Bid[] $bs
 * @property Category $category
 * @property City $city
 * @property User $customer
 * @property User $executor
 * @property Review $review
 * @property User[] $users
 */
class Task extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'task';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['executor_id', 'location', 'city_id', 'lat', 'lng', 'updated_at'], 'default', 'value' => null],
            [['status'], 'default', 'value' => 'new'],
            [['customer_id', 'category_id', 'title', 'description', 'budget', 'expire_date'], 'required'],
            [['customer_id', 'category_id', 'executor_id', 'budget', 'city_id'], 'integer'],
            [['description'], 'string'],
            [['expire_date', 'created_at', 'updated_at'], 'safe'],
            [['lat', 'lng'], 'number'],
            [['title', 'location'], 'string', 'max' => 255],
            [['status'], 'string', 'max' => 32],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => Category::class, 'targetAttribute' => ['category_id' => 'id']],
            [['city_id'], 'exist', 'skipOnError' => true, 'targetClass' => City::class, 'targetAttribute' => ['city_id' => 'id']],
            [['customer_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['customer_id' => 'id']],
            [['executor_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['executor_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'customer_id' => 'Customer ID',
            'category_id' => 'Category ID',
            'executor_id' => 'Executor ID',
            'title' => 'Title',
            'description' => 'Description',
            'budget' => 'Budget',
            'expire_date' => 'Expire Date',
            'status' => 'Status',
            'location' => 'Location',
            'city_id' => 'City ID',
            'lat' => 'Lat',
            'lng' => 'Lng',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[Attachments]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAttachments()
    {
        return $this->hasMany(Attachment::class, ['task_id' => 'id']);
    }

    /**
     * Gets query for [[Bs]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBs()
    {
        return $this->hasMany(Bid::class, ['task_id' => 'id']);
    }

    /**
     * Gets query for [[Category]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(Category::class, ['id' => 'category_id']);
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
     * Gets query for [[Customer]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCustomer()
    {
        return $this->hasOne(User::class, ['id' => 'customer_id']);
    }

    /**
     * Gets query for [[Executor]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getExecutor()
    {
        return $this->hasOne(User::class, ['id' => 'executor_id']);
    }

    /**
     * Gets query for [[Review]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getReview()
    {
        return $this->hasOne(Review::class, ['task_id' => 'id']);
    }

    /**
     * Gets query for [[Users]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(User::class, ['id' => 'user_id'])->viaTable('bid', ['task_id' => 'id']);
    }

}
