# ARES

Client for fetching data from ARES.

## Installation via [Composer](https://getcomposer.org/)

```sh
composer require tady-eu/ares
```

## Usage

```php
<?php
//  ...

$aresClient = new \BG\Ares\Client();

$recordByIC = $aresClient->findOneByIC(88673057); // Returns \BG\Ares\Record

$recordByParameters = $aresClient->findOneByQuery([
    "obchodni_firma" => "Jaromír Navara"
]);  // Returns \BG\Ares\Record

$multipleByParameters = $aresClient->findByQuery([
    "obchodni_firma" => "ASSECO"
]);  // Returns array \BG\Ares\Record[]

```