<?php

namespace Tecactus\Reniec;


use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Tecactus\Reniec\Exception\InvalidDniException;
use Tecactus\Reniec\Exception\UnauthenticatedException;
use Tecactus\Reniec\Exception\ServerErrorException;
use Tecactus\Reniec\Exception\UndefinedErrorException;

class DNI
{
    protected $client;
    protected $baseUri;
    protected $apiToken;

    public function __construct($apiToken)
    {
        $this->baseUri = "https://tecactus.com/";
        $this->apiToken = $apiToken;
        $this->client = new Client(['base_uri' => $this->baseUri, 'verify' => __DIR__.'/cacert.pem', 'headers' => ['Accept' => 'application/json', 'Authorization' => "Bearer {$this->apiToken}"]]);
    }

    public function get($dni, $asArray = false)
    {
    	try {
	        if (!$this->validate($dni)) {
	            throw new InvalidDniException('DNI number seems not to be valid.');
	        }
	        $response = $this->client->request('POST', 'api/reniec/dni', ['query' => [
	            'dni' => $dni
	        ]]);
	        return json_decode($response->getBody()->getContents(), $asArray);
    	} catch (ClientException $e) {
    		$status = $e->getResponse()->getStatusCode();
    		switch ($status) {
    			case 401:
    				throw new UnauthenticatedException('Token seems not to be valid.');
    				break;

    			case 500:
    				throw new ServerErrorException('Server error.');
    				break;
    			
    			default:
    				throw new UndefinedErrorException('An unexpected error has ben ocurred.');
    				break;
    		}
    	}
    }

    protected function validate($value)
    {
        if (is_numeric($value)) {
            return strlen($value) == 8;
        }
        return false;
    }
}