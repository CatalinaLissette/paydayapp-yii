<?php


namespace app\api\v1\controllers;


use app\models\KhipuAccount;
use app\services\KhipuService;
use yii\filters\Cors;
use yii\rest\ActiveController;

class KhipuAccountController extends SafeController
{
    public $modelClass = KhipuAccount::class;
    private KhipuService $khipuService;

    public function __construct(
        $id,
        $module,
        $config = [],
        KhipuService $khipuService
    )
    {
        parent::__construct($id, $module, $config);
        $this->khipuService = $khipuService;
    }

    public function actionCreate()
    {
        $model = new $this->modelClass;

        $data = $this->request->post();

        $model->load(['KhipuAccount' => $data]);

        if($model->save()) {
            return $model;
        }

        return $model->getErrors();

    }

    public function actionSearchByProviderId($provider_id){
        $model = new $this->modelClass;
        return $model::find()->where(['provider_id' => $provider_id])->all();

    }
    public function actionSearchAll(){
        $model = new $this->modelClass;
        return $this->khipuService->searchAll();

    }
}