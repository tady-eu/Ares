<?php
/**
 * @author Jaromír Navara [BG] <web@bagricek.cz>
 * ClientTest.php
 * 9.3.17 3:24
 */

namespace BG\Ares\Tests;

require "src/Client.php";
require "src/AresException.php";
require "src/Record.php";
require "src/Parsers/BasicParser.php";
require "src/Parsers/StandardIdParser.php";

use BG\Ares\Client;

final class ClientTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Client
     */
    private $client;

    protected function setUp()
    {
        $this->client = new Client();
    }

    public function testFindOneByIC()
    {
        $record = $this->client->findOneByIC(88673057);
        $this->assertSame($record->getFirma(), "Jaromír Navara");
    }

    public function testFindOneByQuery(){
        $record = $this->client->findOneByQuery(["obchodni_firma" => "Jaromír Navara"]);
        $this->assertSame($record->getIco(), 88673057);
    }

    public function testFindByQuery(){
        $records = $this->client->findByQuery(["obchodni_firma" => "ASSECO"]);
        $this->assertTrue(count($records) > 1);
    }

}