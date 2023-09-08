<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "provider".
 *
 * @property int $id
 * @property string $createdAt
 * @property string $updatedAt
 * @property int $state
 *
 * @property Commerce[] $commerces
 * @property Notification[] $notifications
 * @property Order[] $orders
 * @property ProviderHasCommerce[] $providerHasCommerces
 * @property User[] $user
 */
class Provider extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'provider';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['state'], 'required'],
            [['createdAt', 'updatedAt'], 'safe'],
            [['state'], 'integer'],
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
        ];
    }

    /**
     * Gets query for [[Commerces]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCommerces()
    {
        return $this->hasMany(Commerce::class, ['id' => 'commerce_id'])->viaTable('provider_has_commerce', ['provider_id' => 'id']);
    }

    /**
     * Gets query for [[Notifications]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getNotifications()
    {
        return $this->hasMany(Notification::class, ['provider_id' => 'id']);
    }

    /**
     * Gets query for [[Orders]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrders()
    {
        return $this->hasMany(Order::class, ['provider_id' => 'id']);
    }

    /**
     * Gets query for [[ProviderHasCommerces]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProviderHasCommerces()
    {
        return $this->hasMany(ProviderHasCommerce::class, ['provider_id' => 'id']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['provider_id' => 'id']);
    }

    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'createdAt',
                'updatedAtAttribute' => 'updatedAt',
                'value' => new Expression('NOW()')
            ]
        ]);
    }

    public function extraFields()
    {
        return ['user'];
    }
}
