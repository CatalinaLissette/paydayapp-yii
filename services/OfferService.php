<?php


namespace app\services;


use app\models\Offer;

class OfferService
{
    /**
     * @var Offer
     */
    private Offer $model;
    public function __construct(
        Offer $model
    )
    {
        $this->model = $model;
    }

    public function create($requestData)
    {
        $this->model->load(['Offer' => $requestData]);

        if($this->model->save()) {
            return $this->model;
        }

        return $this->model->getErrors();


    }

    public function update($id, $requestData)
    {
        $offer = $this->model::findOne($id);

        if ($offer) {
            $offer->load(['Offer' => $requestData]);

            if ($offer->save()) {
                return $offer;
            }

            return $offer->getErrors();
        }

        return null;

    }

    public function getOffersByProviderId($provider_id)
    {
        return $this->model::find()->where(['provider_id' => $provider_id])->all();
    }


}