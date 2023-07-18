<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "khipu_account".
 *
 * @property string $key
 * @property int $provider_id
 * @property int $id
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

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['key', 'provider_id'], 'required'],
            [['provider_id'], 'integer'],
            [['key'], 'string', 'max' => 100],
            [['provider_id'], 'unique'],
            [['key'], 'unique'],
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
            'id' => 'ID',
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
}
