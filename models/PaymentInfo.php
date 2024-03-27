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
 * @property string $request_token
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

    public static function createPayment(User $user, int $requestId, string $requestToken): self
    {
        $instance = new self([
            'token' => 'no-token-yet',
            'date_register' => new Expression('NOW()'),
            'request_id' => $requestId,
            'state' => 1,
            'user_id' => $user->id,
            'request_token' => $requestToken
        ]);
        if ($instance->save()) {
            return $instance;
        }
        throw new \Exception(Json::encode($instance->errors));
    }

    public static function findRequest(string $requestToken): self
    {
        $info = static::findOne(['request_token' => $requestToken]);
        if (!$info) throw new \Exception('request not found');
        return $info;
    }
    public static function disableOther(int $userId): void
    {
        static::updateAll(['state' => 2],['user_id' => $userId]);

    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['token', 'date_register', 'state', 'user_id'], 'required'],
            [['date_register', 'request_token'], 'safe'],
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

    public function clearData()
    {

        $this->request_id = '';
        $this->request_token = '';
    }

    public function setToken(string $token)
    {
        $this->token = $token;
    }
}
