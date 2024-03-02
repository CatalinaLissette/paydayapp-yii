<?php

namespace app\services;

class GetNetClickService
{
    private HttpService $httpService;
    public function __construct(
        HttpService $httpService
    )
    {
        $this->httpService = $httpService;
    }

    public function createSuscription(array $data)
    {


        $response =  $this->httpService->request(
            'POST',
            'https://checkout.test.getnet.cl/api/session/',
            $data
        );
        return $response->data;
    }

    public function getRequestInformation(array $data,string $requestId)
    {

        $response =  $this->httpService->request(
            'POST',
            "https://checkout.test.getnet.cl/api/session/".$requestId,
            $data
        );
        return $response->data;
    }
    public function collect(array $data)
    {

        $response =  $this->httpService->request(
            'POST',
            "https://checkout.test.getnet.cl/api/collect",
            $data
        );
        return $response->data;
    }

    public function invalidate(array $data)
    {

        $response =  $this->httpService->request(
            'POST',
            "https://checkout.test.getnet.cl/api/instrument/invalidate",
            $data
        );
        return $response->data;
    }
}