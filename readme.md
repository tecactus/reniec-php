# RENIEC-PHP
   
## Instalación

Instalar usando composer:

```bash
   composer require tecactus/reniec-php

```

O agregar la siguiente línea a tu archivo composer.json:

```json
   "require": {
       ...
       "tecactus/reniec-php": "1.*"
       ...
   }
```

## Uso

```php
  // incluir el autoloader de vendor
  require 'vendor/autoload.php';

  //crea un objeto de la clase DNI
  $reniecDni = new Tecactus\Reniec\DNI('tu-token-de-acceso-personal');
   
  print_r($reniecDni->get('12345678'));
   
  // para devolver el resultado como un array pasar 'true' como segundo argumento.
  print_r($reniecDni->get('20131312955', true));
   
```

## Token de Acceso Personal

Para crear tokens de acceso personal debes de iniciar sesión en Tecactus:

[https://tecactus.com/auth/login](https://tecactus.com/auth/login)

Si no estas registrado aún, puedes hacerlo en:

[https://tecactus.com/auth/register](https://tecactus.com/auth/register)

Debes de activar tu cuenta si aún no lo has hecho.
Luego ver el panel de gestión de Tokens de acceso en:

[https://tecactus.com/developers/configuracion/tokens](https://tecactus.com/developers/configuracion/tokens)