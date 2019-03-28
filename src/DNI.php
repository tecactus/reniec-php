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
        $this->baseUri = "https://consulta.pe/";
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

    public function validateDigit($dni, $digit)
    {
        return $this->validate($dni) && $this->validateHash($dni, $digit);
    }

    protected function validate($value)
    {
        if (is_numeric($value)) {
            return strlen($value) == 8;
        }
        return false;
    }

    protected function validateHash($v, $h)
    {
        $identificationDocument = $v . '0';
        $addition = 0;
        $hash = [5, 4, 3, 2, 7, 6, 5, 4, 3, 2 ];
        $identificationDocumentLength = strlen($identificationDocument);

        $identificationComponent = substr($identificationDocument, 0, $identificationDocumentLength - 1);

        $identificationComponentLength = strlen($identificationComponent);

        $diff = count($hash) - $identificationComponentLength;

        for ($i = $identificationComponentLength - 1; $i >= 0; $i--)
        {
          $addition += ($identificationComponent[$i] - '0') * $hash[$i + $diff];
        }

        $addition = 11 - ($addition % 11);

        if ($addition == 11)
        {
          $addition = 0;
        }

        $last = $identificationDocument[$identificationDocumentLength - 1];

        $hashValue = '';

        if (ctype_alpha($last))
        {
          $hashLetters = [ 'K', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J' ];
          $hashValue = $hashLetters[$addition];
        }

        else if (is_numeric($last))
        {
          $hashNumbers = [ '6', '7', '8', '9', '0', '1', '1', '2', '3', '4', '5' ];
          $hashValue = $hashNumbers[$addition];
        }

        return $h == $hashValue;
    }
}
