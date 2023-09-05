<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "offer".
 *
 * @property int $id
 * @property string|null $title
 * @property string|null $description
 * @property string|null $img
 * @property int|null $price_offer
 * @property int|null $price
 * @property int|null $is_offer
 * @property int $provider_id
 *
 * @property Provider $provider
 */
class Offer extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'offer';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['price_offer', 'price', 'is_offer', 'provider_id'], 'integer'],
            [['provider_id'], 'required'],
            [['title'], 'string', 'max' => 100],
            [['description', 'img'], 'string', 'max' => 255],
            [['provider_id'], 'exist', 'skipOnError' => true, 'targetClass' => Provider::class, 'targetAttribute' => ['provider_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'description' => 'Description',
            'img' => 'Img',
            'price_offer' => 'Price Offer',
            'price' => 'Price',
            'is_offer' => 'Is Offer',
            'provider_id' => 'Provider ID',
        ];
    }
    public function behaviors()
    {
        return ArrayHelper::merge([
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'createdAt',
                'updatedAtAttribute' => 'updatedAt',
                'value' => new Expression('NOW()')
            ]
        ], parent::behaviors());
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

    public function getOffersByProviderId($provider_id)
    {
    }
}
