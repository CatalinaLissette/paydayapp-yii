<?php

namespace app\models;

use app\enums\StateEnum;
use app\enums\StateOrderEnum;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Exception;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

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

    public static function payment(int $id, string $requestId)
    {
        $model = Quote::findOne($id);
        $model->state = StateOrderEnum::PAYED;
        $model->paymentId = $requestId;
        $model->update();
    }

    public static function validatePayment(int $id)
    {
        $model = Quote::findOne($id);
        if(!$model)
            throw new Exception('no se encuentra la cuota en el sistema');
        if($model->state == StateOrderEnum::PAYED)
            throw new Exception('la cuota seleccionada ya se encuentra pagada');

    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['order_id', 'state', 'quoteNumber', 'payDate', 'quoteAmount'], 'required'],
            [['order_id', 'state', 'quoteNumber', 'quoteAmount'], 'integer'],
            [['createdAt', 'updatedAt', 'payDate'], 'safe'],
            [['paymentId'], 'string', 'max' => 45],
            [['order_id'], 'exist', 'skipOnError' => true, 'targetClass' => Order::class, 'targetAttribute' => ['order_id' => 'id']],
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
