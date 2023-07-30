<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "commerce".
 *
 * @property int $id
 * @property string $createdAt
 * @property string $updatedAt
 * @property int $state
 * @property string $businessType
 *
 * @property Notification[] $notifications
 * @property Order[] $orders
 * @property ProviderHasCommerce[] $providerHasCommerces
 * @property Provider[] $providers
 * @property User[] $users
 */
class Commerce extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'commerce';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['state', 'businessType'], 'required'],
            [['createdAt', 'updatedAt'], 'safe'],
            [['state'], 'integer'],
            [['businessType'], 'string', 'max' => 45],
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
            'businessType' => 'Business Type',
        ];
    }

    /**
     * Gets query for [[Notifications]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getNotifications()
    {
        return $this->hasMany(Notification::class, ['commerce_id' => 'id']);
    }

    /**
     * Gets query for [[Orders]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrders()
    {
        return $this->hasMany(Order::class, ['commerce_id' => 'id']);
    }

    /**
     * Gets query for [[ProviderHasCommerces]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProviderHasCommerces()
    {
        return $this->hasMany(ProviderHasCommerce::class, ['commerce_id' => 'id']);
    }

    /**
     * Gets query for [[Providers]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProviders()
    {
        return $this->hasMany(Provider::class, ['id' => 'provider_id'])->viaTable('provider_has_commerce', ['commerce_id' => 'id']);
    }

    /**
     * Gets query for [[Users]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(User::class, ['commerce_id' => 'id']);
    }
}
