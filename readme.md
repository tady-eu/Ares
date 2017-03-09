# ARES
[![Latest stable](https://img.shields.io/packagist/v/tady-eu/ares.svg?style=flat-square)](https://packagist.org/packages/dfridrich/ares)

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
Available query parameters can be found at [http://wwwinfo.mfcr.cz/ares/ares_xml_standard.html.cz](http://wwwinfo.mfcr.cz/ares/ares_xml_standard.html.cz)