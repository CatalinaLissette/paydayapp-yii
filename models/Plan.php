<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "plan".
 *
 * @property int $id
 * @property string $createdAt
 * @property string $updatedAt
 * @property int $state
 * @property int $duration
 * @property string $name
 *
 * @property CommerceHasPlan[] $commerceHasPlans
 * @property User[] $users
 */
class Plan extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'plan';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['createdAt', 'updatedAt', 'state', 'duration', 'name'], 'required'],
            [['createdAt', 'updatedAt'], 'safe'],
            [['state', 'duration'], 'integer'],
            [['name'], 'string', 'max' => 45],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'createdAt' => 'Created At',
            'updatedAt' => 'Updated At',
            'state' => 'State',
            'duration' => 'Duration',
            'name' => 'Name',
        ];
    }

    /**
     * Gets query for [[CommerceHasPlans]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCommerceHasPlans()
    {
        return $this->hasMany(CommerceHasPlan::class, ['plan_id' => 'id']);
    }

    /**
     * Gets query for [[Users]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(User::class, ['id' => 'user_id'])->viaTable('commerce_has_plan', ['plan_id' => 'id']);
    }
}
