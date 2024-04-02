<?php

namespace app\models;

use Ramsey\Uuid\Uuid;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string $email
 * @property string $password_hash
 * @property int $state
 * @property string $createdAt
 * @property string $updatedAt
 * @property string $rut
 * @property string $name
 * @property string $businessName
 * @property string $address
 * @property string $supervisor
 * @property string $phone
 * @property string $uuid
 * @property int $commune_id
 * @property string $profile
 * @property string|null $businessType
 *
 * @property User[] $commerces
 * @property Commune $commune
 * @property KhipuAccount[] $khipuAccounts
 * @property Order[] $orders
 * @property Order[] $orders0
 * @property PaymentInfo[] $paymentInfos
 * @property ProviderHasCommerce[] $providerHasCommerces
 * @property ProviderHasCommerce[] $providerHasCommerces0
 * @property User[] $providers
 */
class User extends \yii\db\ActiveRecord implements IdentityInterface
{
    public string $password, $rePassword;
    const PROFILE_PROVIDER = 'PROVIDER';
    const PROFILE_COMMERCE = 'COMMERCE';

    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 2;

    static function createProvider(array $data): User
    {
        $user = new self($data);
        $user->profile = User::PROFILE_PROVIDER;
        $user->uuid = Uuid::uuid4()->toString();

        if ($user->save()) {
            return $user;
        }
        throw new \Exception(Json::encode($user->errors));
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user';
    }

    public static function createCommerce(array $data)
    {
        $user = new self($data);
        $user->profile = User::PROFILE_COMMERCE;
        $user->uuid = Uuid::uuid4()->toString();
        if ($user->save()) {
            return $user;
        }
        throw new \Exception(Json::encode($user->errors));
    }

    public function behaviors()
    {
        return ArrayHelper::merge([
            'timestamp' => [
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
            [['email', 'state', 'rut', 'name', 'businessName', 'address', 'supervisor', 'phone', 'uuid', 'commune_id', 'profile'], 'required'],
            [['state', 'commune_id'], 'integer'],
            [['createdAt', 'updatedAt'], 'safe'],
            [['profile'], 'string'],
            [['email', 'phone'], 'string', 'max' => 45],
            [['password_hash'], 'string', 'max' => 64],
            [['rut'], 'string', 'max' => 13],
            [['name', 'businessName', 'address', 'businessType'], 'string', 'max' => 250],
            [['supervisor'], 'string', 'max' => 150],
            [['uuid'], 'string', 'max' => 36],
            [['commune_id'], 'exist', 'skipOnError' => true, 'targetClass' => Commune::class, 'targetAttribute' => ['commune_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'email' => 'Email',
            'password_hash' => 'Password Hash',
            'state' => 'State',
            'createdAt' => 'Created At',
            'updatedAt' => 'Updated At',
            'rut' => 'Rut',
            'name' => 'Name',
            'businessName' => 'Business Name',
            'address' => 'Address',
            'supervisor' => 'Supervisor',
            'phone' => 'Phone',
            'uuid' => 'Uuid',
            'commune_id' => 'Commune ID',
            'profile' => 'Profile',
            'businessType' => 'Business Type',
        ];
    }

    /**
     * Gets query for [[Commerces]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCommerces()
    {
        return $this->hasMany(User::class, ['id' => 'commerce_id'])->viaTable('provider_has_commerce', ['provider_id' => 'id']);
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
     * Gets query for [[KhipuAccounts]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getKhipuAccounts()
    {
        return $this->hasMany(KhipuAccount::class, ['user_id' => 'id']);
    }

    /**
     * Gets query for [[Orders]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrders()
    {
        return $this->hasMany(Order::class, ['provider_id' => 'id']);
    }

    /**
     * Gets query for [[Orders0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrders0()
    {
        return $this->hasMany(Order::class, ['commerce_id' => 'id']);
    }

    /**
     * Gets query for [[PaymentInfos]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPaymentInfos()
    {
        return $this->hasMany(PaymentInfo::class, ['user_id' => 'id']);
    }

    /**
     * Gets query for [[ProviderHasCommerces]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProviderHasCommerces()
    {
        return $this->hasMany(ProviderHasCommerce::class, ['provider_id' => 'id']);
    }

    /**
     * Gets query for [[ProviderHasCommerces0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProviderHasCommerces0()
    {
        return $this->hasMany(ProviderHasCommerce::class, ['commerce_id' => 'id']);
    }

    /**
     * Gets query for [[Providers]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProviders()
    {
        return $this->hasMany(User::class, ['id' => 'provider_id'])->viaTable('provider_has_commerce', ['commerce_id' => 'id']);
    }

    public function beforeSave($insert)
    {
        if ($insert) {
            $this->checkPasswordEq($this->password, $this->rePassword);
            $this->password_hash = Yii::$app->security->generatePasswordHash($this->password);
        }
        return parent::beforeSave($insert);
    }

    public function checkPasswordEq(string $password, string $rePassword)
    {
        if ($password === '' || $rePassword === '' || $password !== $rePassword)
            throw new \Exception('contraseñas no son iguales.');
    }

    public static function findIdentity($id)
    {
        return User::findOne(['id' => $id, 'state' => self::STATUS_ACTIVE]);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        $token = Yii::$app->jwt->getParser()->parse((string)$token);
        return static::find()
            ->where(['uuid' => (string)$token->getClaim('uid'), 'state' => self::STATUS_ACTIVE])
            ->one();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAuthKey()
    {
        //TODO: implement auth key <-> remember password
    }

    public function validateAuthKey($authKey)
    {
        //TODO: validate auth key
    }

    public function getUserType(): string
    {
        return strtolower($this->profile);
    }

    public function getRequestId(): int
    {
        if (!$this->paymentInfos)
            throw new \Exception('solicitud no encontrada');
        return $this->paymentInfos[0]->request_id;
    }
}
