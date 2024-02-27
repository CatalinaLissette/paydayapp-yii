<?php

namespace app\services;

use yii\httpclient\Client;
use yii\httpclient\Request;
use yii\httpclient\Response;
use yii\base\Component;

class HttpService extends Component
{
    /**
     * @var Client|null Cliente HTTP
     */
    private $httpClient;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->httpClient = new Client();
    }

    /**
     * Realiza una solicitud HTTP.
     *
     * @param string $method Método de la solicitud (GET, POST, PUT, DELETE, etc.)
     * @param string $url URL de la API
     * @param array $data Datos para enviar en la solicitud (opcional)
     * @param array $headers Encabezados adicionales para enviar en la solicitud (opcional)
     * @return Response Respuesta de la solicitud
     */
    public function request(string $method, string $url, array $data = [], array $headers = []): Response
    {
        return $this->createRequest()
            ->setMethod($method)
            ->setUrl($url)
            ->setData($data)
            ->addHeaders($headers)
            ->send();

    }

    /**
     * Crea una instancia de solicitud HTTP.
     *
     * @return Request Instancia de solicitud HTTP
     */
    private function createRequest(): Request
    {
        return $this->httpClient->createRequest();
    }
}