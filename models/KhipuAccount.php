<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "khipu_account".
 *
 * @property string $key
 * @property int $provider_id
 * @property int $id
 * @property int $receiver_id
 * @property string $reference_id
 *
 * @property Provider $provider
 */
class KhipuAccount extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'khipu_account';
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
    public function rules()
    {
        return [
            [['key', 'provider_id','reference_id'], 'required'],
            [['provider_id','receiver_id'], 'integer'],
            [['key'], 'string', 'max' => 100],
            [['reference_id'], 'string', 'max' => 36],
            [['provider_id'], 'unique'],
            [['key'], 'unique'],
            [['receiver_id'], 'unique'],
            [['reference_id'], 'unique'],
            [['provider_id'], 'exist', 'skipOnError' => true, 'targetClass' => Provider::class, 'targetAttribute' => ['provider_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'key' => 'Key',
            'provider_id' => 'Provider ID',
            'receiver_id' => 'Receiver ID',
            'id' => 'ID',
            'createdAt' => 'Created At',
            'updatedAt' => 'Updated At',
        ];
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
    public function extraFields()
    {
        return ['provider'];
    }
}
