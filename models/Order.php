<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "order".
 *
 * @property int $provider_id
 * @property int $commerce_id
 * @property int $id
 * @property string $createdAt
 * @property string $updatedAt
 * @property int $state
 * @property int $totalAmount
 * @property string|null $observation
 *
 * @property User $commerce
 * @property User $provider
 * @property Quote[] $quotes
 */
class Order extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'order';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['provider_id', 'commerce_id', 'state', 'totalAmount'], 'required'],
            [['provider_id', 'commerce_id', 'state', 'totalAmount'], 'integer'],
            [['createdAt', 'updatedAt'], 'safe'],
            [['observation'], 'string', 'max' => 45],
            [['commerce_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['commerce_id' => 'id']],
            [['provider_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['provider_id' => 'id']],
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
            'totalAmount' => 'Total Amount',
            'observation' => 'Observation',
        ];
    }

    /**
     * Gets query for [[Commerce]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCommerce()
    {
        return $this->hasOne(User::class, ['id' => 'commerce_id']);
    }

    /**
     * Gets query for [[Provider]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProvider()
    {
        return $this->hasOne(User::class, ['id' => 'provider_id']);
    }

    /**
     * Gets query for [[Quotes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getQuotes()
    {
        return $this->hasMany(Quote::class, ['order_id' => 'id']);
    }

    public function extraFields()
    {
        return [
            'quotes' // Nombre de la relación que deseas incluir como extraField
        ];
    }
}
