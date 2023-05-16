<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "quotes".
 *
 * @property int $order_id
 * @property int $id
 * @property string $createdAt
 * @property string $updatedAt
 * @property int $state
 * @property int $quoteNumber
 * @property string $payDate
 * @property string|null $paymentId
 * @property int $quoteAmount
 *
 * @property Order $order
 */
class Quote extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'quotes';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['order_id', 'createdAt', 'updatedAt', 'state', 'quoteNumber', 'payDate', 'quoteAmount'], 'required'],
            [['order_id', 'state', 'quoteNumber', 'quoteAmount'], 'integer'],
            [['createdAt', 'updatedAt', 'payDate'], 'safe'],
            [['paymentId'], 'string', 'max' => 45],
            [['order_id'], 'exist', 'skipOnError' => true, 'targetClass' => Order::class, 'targetAttribute' => ['order_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'order_id' => 'Order ID',
            'id' => 'ID',
            'createdAt' => 'Created At',
            'updatedAt' => 'Updated At',
            'state' => 'State',
            'quoteNumber' => 'Quote Number',
            'payDate' => 'Pay Date',
            'paymentId' => 'Payment ID',
            'quoteAmount' => 'Quote Amount',
        ];
    }

    /**
     * Gets query for [[Order]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrder()
    {
        return $this->hasOne(Order::class, ['id' => 'order_id']);
    }
}
