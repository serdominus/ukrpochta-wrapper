<?php
/**
 * Ukrposhta API wrapper (v2)
 * Автор: SerDominuS
 * Версія: 1.0.0
 * Рік: 2025
 */

namespace Ukrpochta;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class PochtaV2
{
    private $apiBase = 'https://www.ukrposhta.ua/ecom/';
    private $version = '0.0.1';

    private $tokens = [
        'ecom'         => '',
        'tracking'     => '',
        'counterparty' => '',
    ];

    private $mode = 'prod'; // або 'sandbox'
    private $tokenMap = [
        'addresses'        => 'ecom',
        'clients'          => 'counterparty',
        'shipment-groups'  => 'ecom',
        'shipments'        => 'ecom',
        'tracking'         => 'tracking',
    ];

    public function __construct(array $tokens = [], $mode = 'prod')
    {
        $this->setTokens($tokens, $mode);
    }

    public function setTokens(array $tokens, $mode = 'prod')
    {
        $this->tokens = array_merge($this->tokens, $tokens);
        $this->mode = $mode;
        return $this;
    }

    private function prepare($data)
    {
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    private function createUrl($method, $param = '')
    {
        $url = rtrim($this->apiBase, '/') . '/' . $this->version . '/' . $method;
        if ($param) {
            $url .= '/' . ltrim($param, '/');
        }
        return $url;
    }

    private function requestData($method, $data = '', $param = '', $type = 'post', $tokenType = null)
    {
        if (!$tokenType && isset($this->tokenMap[$method])) {
            $tokenType = $this->tokenMap[$method];
        }

        $token = $this->tokens[$tokenType] ?? null;
        if (!$token) {
            throw new \Exception("Token for '$tokenType' is not set.");
        }

        $url = $this->createUrl($method, $param);
        $client = new Client([
            'headers' => [
                'Content-Type'  => 'application/json',
                'Authorization' => 'Bearer ' . $token,
            ]
        ]);

        try {
            $options = $data ? ['body' => $this->prepare($data)] : [];
            $response = match ($type) {
                'post'   => $client->post($url, $options),
                'get'    => $client->get($url),
                'put'    => $client->put($url, $options),
                'delete' => $client->delete($url),
                default  => throw new \Exception("Invalid request type: $type"),
            };
            return $response->getBody()->getContents();
        } catch (RequestException $e) {
            return $e->getResponse()?->getBody()?->getContents() ?: 'Request failed.';
        }
    }

    public function createAddress(array $data)
    {
        return $this->requestData('addresses', $data);
    }

    public function getAddress($id)
    {
        return $this->requestData('addresses', '', $id, 'get');
    }

    public function createClient(array $data)
    {
        return $this->requestData('clients', $data, '?token=' . $this->tokens['counterparty'], 'post', 'counterparty');
    }

    public function getClient($id)
    {
        return $this->requestData('clients', '', $id . '?token=' . $this->tokens['counterparty'], 'get', 'counterparty');
    }

    public function createParcel(array $data)
    {
        return $this->requestData('shipments', $data, '?token=' . $this->tokens['counterparty'], 'post', 'ecom');
    }

    public function getParcel($uuid)
    {
        return $this->requestData('shipments', '', $uuid . '?token=' . $this->tokens['counterparty'], 'get', 'ecom');
    }

    public function getTracking($barcode)
    {
        return $this->requestData('tracking', '', $barcode, 'get', 'tracking');
    }

    public function createFormPDF($uuid, $path)
    {
        $pdf = $this->requestData('shipments', '', $uuid . '/form?token=' . $this->tokens['counterparty'], 'get', 'ecom');
        file_put_contents($path, $pdf);
        return $path;
    }
}
