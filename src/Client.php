<?php
/**
 * @author Jaromír Navara [BG] <web@bagricek.cz>
 * AresFetch.php
 * 8.3.17 23:49
 */

declare(strict_types = 1);

namespace BG\Ares;

use BG\Ares\Parsers\BasicParser;
use BG\Ares\Parsers\StandardIdParser;
use GuzzleHttp\Psr7\Response;

/**
 * Class Client
 * @package BG\Ares
 */
class Client
{

    /**
     * API endpoint we use to look for what ICs to fetch
     * @see http://wwwinfo.mfcr.cz/ares/ares_xml_standard.html.cz
     */
    const STANDARD_URL = 'http://wwwinfo.mfcr.cz/cgi-bin/ares/darv_std.cgi';

    /**
     * API endpoint for fetching data by IC
     * @see http://wwwinfo.mfcr.cz/ares/ares_xml_basic.html.cz
     */
    const BASIC_URL = 'http://wwwinfo.mfcr.cz/cgi-bin/ares/darv_bas.cgi';

    const DEFAULT_QUERY_PARAMETERS = [
        "czk" => "utf"
    ];

    /** @var \GuzzleHttp\Client */
    protected $client;

    public function __construct()
    {
        $this->client = new \GuzzleHttp\Client([
            "base_uri" => self::STANDARD_URL
        ]);
    }

    /**
     * @param array $query Possible parameters of query as follows:
     * @see http://wwwinfo.mfcr.cz/ares/ares_xml_standard.html.cz For allowed parameters
     * @throws AresException
     * @return Record[]|null
     */
    public function findByQuery(array $query)
    {
        $API_ENDPOINT = isset($query["ico"]) ? self::BASIC_URL : self::STANDARD_URL;

        /** @var Response $res */
        $res = $this->client->get($API_ENDPOINT, ["query" => array_merge(self::DEFAULT_QUERY_PARAMETERS, $query)]);

        if ($res->getStatusCode() !== 200) throw new AresException("Cannot fetch data! HTTP CODE " . $res->getStatusCode());

        $xmlElem = new \SimpleXMLElement((string)$res->getBody());

        return isset($query["ico"]) ? BasicParser::parseXml($xmlElem) : $this->fetchByICs(StandardIdParser::parseXml($xmlElem));
    }

    /**
     * @param int[]|null $ICs
     * @return array|null
     */
    protected function fetchByICs($ICs){
        if($ICs === null) return null;

        $records = [];
        foreach ($ICs as $ic){
            $records[] = $this->findOneByIC($ic);
        }

        return $records;
    }

    /**
     * @param array $query
     * @see fetchByQuery method for query params
     * @return Record|null
     */
    public function findOneByQuery(array $query)
    {
        $res = $this->findByQuery($query);
        return $res !== null ? $res[0] : null;
    }

    /**
     * @param int $ic
     * @return Record|null
     */
    public function findOneByIC(int $ic)
    {
        return $this->findOneByQuery(["ico" => intval($ic)]);
    }

}