<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

/**
 * This is the model class for table "provider_has_commerce".
 *
 * @property int $provider_id
 * @property int $commerce_id
 * @property int $credit
 * @property string|null $created_at
 * @property string $updated_at
 * @property int $state
 *
 * @property User $commerce
 * @property User $provider
 */
class ProviderHasCommerce extends \yii\db\ActiveRecord
{
    const STATE_APPROVED = 1;
    const STATE_PENDING = 2;

    public static function validateState($state)
    {
        if (!in_array($state, [self::STATE_APPROVED, self::STATE_PENDING])) {
            throw new \Exception('invalid state');
        }
    }

    public function behaviors()
    {
        return ArrayHelper::merge([
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' => new Expression('NOW()')
            ]
        ], parent::behaviors());
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'provider_has_commerce';
    }

    public static function createEnrollment(array $post): self
    {
        $model = new self();
        $model->credit = 0;
        $model->state = self::STATE_PENDING;
        $model->load(['ProviderHasCommerce' => $post]);
        if ($model->save()) {
            return $model;
        }
        throw new \Exception(Json::encode($model->errors));
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['provider_id', 'commerce_id', 'state'], 'required'],
            [['provider_id', 'commerce_id', 'state', 'credit'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['provider_id', 'commerce_id'], 'unique', 'targetAttribute' => ['provider_id', 'commerce_id']],
            [['provider_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['provider_id' => 'id']],
            [['commerce_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['commerce_id' => 'id']],
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
            'credit' => 'Credit',
            'created_at' => 'Created At',
            'state' => 'State',
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
}
