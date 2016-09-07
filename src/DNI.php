<?php

namespace Tecactus\Reniec;


use GuzzleHttp\Client;
use Tecactus\Reniec\Exception\InvalidDniException;

class DNI
{
    protected $client;
    protected $baseUri;
    protected $apiToken;

    public function __construct($apiToken)
    {
        $this->baseUri = "https://tecactus.com/";
        $this->apiToken = $apiToken;
        $this->client = new Client(['base_uri' => $this->baseUri, 'headers' => ['Accept' => 'application/json', 'Authorization' => 'Bearer ' . $this->apiToken]]);
    }

    public function get($dni, $asArray = false)
    {
        if (!$this->validate($dni)) {
            throw new InvalidDniException('DNI number seems not to be valid.');
        }
        $response = $this->client->request('POST', 'api/reniec/dni', ['query' => [
            'dni' => $dni
        ]]);
        return json_decode($response->getBody()->getContents(), $asArray);
    }

    protected function validate($value)
    {
        if (is_numeric($value)) {
            return strlen($value) == 8;
        }
        return false;
    }
}