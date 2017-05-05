<?php

namespace DNI;

use Dotenv\Dotenv;
use Tecactus\Reniec\DNI;
use Tecactus\Reniec\Exception\InvalidDniException;
use Tecactus\Reniec\Exception\UnauthenticatedException;

class DNITest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var String
     */
    protected $validDni;
    /**
     * @var String
     */
    protected $invalidDni;
    /**
     * @var String
     */
    protected $validToken;

    /**
     * @var String
     */
    protected $invalidToken;

    public function setUp()
    {
    	$dotenv = new Dotenv(__DIR__ . '/../../');
		$dotenv->load();
        $this->validDni = '46126033';
        $this->invalidDni = 'string';
        $this->validToken = getenv('TECACTUS_TOKEN');
        $this->invalidToken = 'invalid-token';
    }

	public function testCanBeCreated()
    {
        $this->assertInstanceOf(
            DNI::class,
            new DNI('')
        );
    }

    public function testInvalidDniNumber()
    {
    	$this->setExpectedException(InvalidDniException::class);
    	(new DNI(''))->get($this->invalidDni);
    }

    public function testInvalidToken()
    {
    	$this->setExpectedException(UnauthenticatedException::class);
    	(new DNI($this->invalidToken))->get($this->validDni);
    }

    public function testPersonResponse()
    {
    	$person = (new DNI($this->validToken))->get($this->validDni, true);
    	$this->assertArrayHasKey('dni', $person);
    	$this->assertArrayHasKey('nombres', $person);
    	$this->assertArrayHasKey('apellido_paterno', $person);
    	$this->assertArrayHasKey('apellido_materno', $person);
    	$this->assertArrayHasKey('caracter_verificacion', $person);
    	$this->assertArrayHasKey('caracter_verificacion_anterior', $person);
    }
}