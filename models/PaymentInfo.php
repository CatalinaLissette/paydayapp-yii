<?php

namespace app\models;

use Yii;
use yii\db\Expression;
use yii\helpers\Json;

/**
 * This is the model class for table "payment_info".
 *
 * @property int $id
 * @property string $token
 * @property string $date_register
 * @property int|null $request_id
 * @property int $state
 * @property int $user_id
 *
 * @property User $user
 */
class PaymentInfo extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'payment_info';
    }

    public static function createPayment(User $user, $requestId): self
    {
        $instance = new self([
            'token' => 'no-token-yet',
            'date_register' => new Expression('NOW()'),
            'request_id' => $requestId,
            'state' => 1,
            'user_id' => $user->id,
        ]);
        if ($instance->save()) {
            return $instance;
        }
        throw new \Exception(Json::encode($instance->errors));
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['token', 'date_register', 'state', 'user_id'], 'required'],
            [['date_register'], 'safe'],
            [['request_id', 'state', 'user_id'], 'integer'],
            [['token'], 'string', 'max' => 64],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'token' => 'Token',
            'date_register' => 'Date Register',
            'request_id' => 'Request ID',
            'state' => 'State',
            'user_id' => 'User ID',
        ];
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
