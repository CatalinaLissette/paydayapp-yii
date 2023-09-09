<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "provider_has_commerce".
 *
 * @property int $provider_id
 * @property int $commerce_id
 * @property string $createdAt
 * @property string $updatedAt
 * @property int $state
 *
 * @property Commerce $commerce
 * @property Provider $provider
 */
class ProviderHasCommerce extends \yii\db\ActiveRecord
{
    CONST STATE_APPROBED = 1;
    CONST STATE_PENDING = 2;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'provider_has_commerce';
    }

    public static function enroll(array $post): self
    {
        $model = new self();
        $model->load(['ProviderHasCommerce' => $post]);
        $model->state = self::STATE_PENDING;
        return $model;
    }

    public static function validateState(int $state)
    {
        if (!in_array($state, [self::STATE_APPROBED, self::STATE_PENDING])) {
            throw new \Exception('invalid state');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['provider_id', 'commerce_id', 'state'], 'required'],
            [['provider_id', 'commerce_id', 'state'], 'integer'],
            [['createdAt', 'updatedAt'], 'safe'],
            [['provider_id', 'commerce_id'], 'unique', 'targetAttribute' => ['provider_id', 'commerce_id']],
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
            'createdAt' => 'Created At',
            'updatedAt' => 'Updated At',
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

    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'timestamp'=> [
                'class'=>TimestampBehavior::class,
                'updatedAtAttribute' => 'updatedAt',
                'createdAtAttribute' => 'createdAt',
                'value' => new Expression('NOW()')
            ]
        ]);
    }
}
