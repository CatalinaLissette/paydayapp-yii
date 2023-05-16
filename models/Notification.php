<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "notification".
 *
 * @property int $provider_id
 * @property int $commerce_id
 * @property int $id
 * @property string $createdAt
 * @property string $updatedAt
 * @property int $state
 * @property string $title
 * @property string $detail
 * @property int $type
 *
 * @property Commerce $commerce
 * @property Provider $provider
 */
class Notification extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'notification';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['provider_id', 'commerce_id', 'createdAt', 'updatedAt', 'state', 'title', 'detail', 'type'], 'required'],
            [['provider_id', 'commerce_id', 'state', 'type'], 'integer'],
            [['createdAt', 'updatedAt'], 'safe'],
            [['title', 'detail'], 'string', 'max' => 45],
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
            'commerce_id' => 'Commerce ID',
            'id' => 'ID',
            'createdAt' => 'Created At',
            'updatedAt' => 'Updated At',
            'state' => 'State',
            'title' => 'Title',
            'detail' => 'Detail',
            'type' => 'Type',
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
