<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "region".
 *
 * @property int $id
 * @property string $name
 * @property int $state
 * @property string $createdAt
 * @property string $updatedAt
 *
 * @property Commune[] $communes
 */
class Region extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'region';
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
            [['name', 'state'], 'required'],
            [['state'], 'integer'],
            [['createdAt', 'updatedAt'], 'safe'],
            [['name'], 'string', 'max' => 45],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'state' => 'State',
            'createdAt' => 'Created At',
            'updatedAt' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[Communes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCommunes()
    {
        return $this->hasMany(Commune::class, ['region_id' => 'id']);
    }

    public function extraFields()
    {
        return ArrayHelper::merge(parent::extraFields(), ['communes']);
    }
}
