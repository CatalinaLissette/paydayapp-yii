<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "provider_has_commerce".
 *
 * @property int $provider_id
 * @property string $createdAt
 * @property string $updatedAt
 * @property int $state
 * @property int $commerce_id
 * @property int|null $credit
 *
 * @property Commerce $commerce
 * @property Provider $provider
 */
class ProviderHasCommerce extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'provider_has_commerce';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['provider_id', 'createdAt', 'updatedAt', 'state', 'commerce_id'], 'required'],
            [['provider_id', 'state', 'commerce_id', 'credit'], 'integer'],
            [['createdAt', 'updatedAt'], 'safe'],
            [['commerce_id'], 'exist', 'skipOnError' => true, 'targetClass' => Commerce::class, 'targetAttribute' => ['commerce_id' => 'id']],
            [['provider_id'], 'exist', 'skipOnError' => true, 'targetClass' => Provider::class, 'targetAttribute' => ['provider_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'provider_id' => 'Provider ID',
            'createdAt' => 'Created At',
            'updatedAt' => 'Updated At',
            'state' => 'State',
            'commerce_id' => 'Commerce ID',
            'credit' => 'Credit',
        ];
    }

    /**
     * Gets query for [[Commerce]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCommerce()
    {
        return $this->hasOne(Commerce::class, ['id' => 'commerce_id']);
    }

    /**
     * Gets query for [[Provider]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProvider()
    {
        return $this->hasOne(Provider::class, ['id' => 'provider_id']);
    }
}
