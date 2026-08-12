<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "attachment".
 *
 * @property int $id
 * @property int $task_id
 * @property string $file_path
 * @property string $original_name
 * @property string|null $mime_type
 * @property int|null $size_bytes
 * @property string $created_at
 *
 * @property Task $task
 */
class Attachment extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'attachment';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['mime_type', 'size_bytes'], 'default', 'value' => null],
            [['task_id', 'file_path', 'original_name'], 'required'],
            [['task_id', 'size_bytes'], 'integer'],
            [['created_at'], 'safe'],
            [['file_path', 'original_name', 'mime_type'], 'string', 'max' => 255],
            [['task_id'], 'exist', 'skipOnError' => true, 'targetClass' => Task::class, 'targetAttribute' => ['task_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'task_id' => 'Task ID',
            'file_path' => 'File Path',
            'original_name' => 'Original Name',
            'mime_type' => 'Mime Type',
            'size_bytes' => 'Size Bytes',
            'created_at' => 'Created At',
        ];
    }

    /**
     * Gets query for [[Task]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTask()
    {
        return $this->hasOne(Task::class, ['id' => 'task_id']);
    }

}
