<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "commerce_has_plan".
 *
 * @property int $plan_id
 * @property int $user_id
 * @property int $id
 * @property string $createdAt
 * @property string $updatedAt
 * @property string $finishedDate
 *
 * @property Plan $plan
 * @property User $user
 */
class CommerceHasPlan extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'commerce_has_plan';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['plan_id', 'user_id', 'id', 'createdAt', 'updatedAt', 'finishedDate'], 'required'],
            [['plan_id', 'user_id', 'id'], 'integer'],
            [['createdAt', 'updatedAt', 'finishedDate'], 'safe'],
            [['plan_id', 'user_id'], 'unique', 'targetAttribute' => ['plan_id', 'user_id']],
            [['plan_id'], 'exist', 'skipOnError' => true, 'targetClass' => Plan::class, 'targetAttribute' => ['plan_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'plan_id' => 'Plan ID',
            'user_id' => 'User ID',
            'id' => 'ID',
            'createdAt' => 'Created At',
            'updatedAt' => 'Updated At',
            'finishedDate' => 'Finished Date',
        ];
    }

    /**
     * Gets query for [[Plan]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPlan()
    {
        return $this->hasOne(Plan::class, ['id' => 'plan_id']);
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
