<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property int|null $commerce_id
 * @property int|null $provider_id
 * @property int $commune_id
 * @property string $email
 * @property string $hash
 * @property string|null $hashedRt
 * @property int $state
 * @property string $createdAt
 * @property string $updatedAt
 * @property string $rut
 * @property string $name
 * @property string $businessName
 * @property string $address
 * @property string $supervisor
 * @property string $phone
 *
 * @property Commerce $commerce
 * @property CommerceHasPlan[] $commerceHasPlans
 * @property Commune $commune
 * @property Pago[] $pagos
 * @property Plan[] $plans
 * @property Provider $provider
 */
class User extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['commerce_id', 'provider_id', 'commune_id', 'state'], 'integer'],
            [['commune_id', 'email', 'hash', 'state', 'createdAt', 'updatedAt', 'rut', 'name', 'businessName', 'address', 'supervisor', 'phone'], 'required'],
            [['createdAt', 'updatedAt'], 'safe'],
            [['email', 'hash', 'hashedRt', 'rut', 'name', 'businessName', 'address', 'supervisor', 'phone'], 'string', 'max' => 45],
            [['commerce_id'], 'exist', 'skipOnError' => true, 'targetClass' => Commerce::class, 'targetAttribute' => ['commerce_id' => 'id']],
            [['commune_id'], 'exist', 'skipOnError' => true, 'targetClass' => Commune::class, 'targetAttribute' => ['commune_id' => 'id']],
            [['provider_id'], 'exist', 'skipOnError' => true, 'targetClass' => Provider::class, 'targetAttribute' => ['provider_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'commerce_id' => 'Commerce ID',
            'provider_id' => 'Provider ID',
            'commune_id' => 'Commune ID',
            'email' => 'Email',
            'hash' => 'Hash',
            'hashedRt' => 'Hashed Rt',
            'state' => 'State',
            'createdAt' => 'Created At',
            'updatedAt' => 'Updated At',
            'rut' => 'Rut',
            'name' => 'Name',
            'businessName' => 'Business Name',
            'address' => 'Address',
            'supervisor' => 'Supervisor',
            'phone' => 'Phone',
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
     * Gets query for [[CommerceHasPlans]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCommerceHasPlans()
    {
        return $this->hasMany(CommerceHasPlan::class, ['user_id' => 'id']);
    }

    /**
     * Gets query for [[Commune]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCommune()
    {
        return $this->hasOne(Commune::class, ['id' => 'commune_id']);
    }

    /**
     * Gets query for [[Pagos]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPagos()
    {
        return $this->hasMany(Pago::class, ['user_id' => 'id']);
    }

    /**
     * Gets query for [[Plans]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPlans()
    {
        return $this->hasMany(Plan::class, ['id' => 'plan_id'])->viaTable('commerce_has_plan', ['user_id' => 'id']);
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
