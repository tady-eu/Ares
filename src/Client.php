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

use function GuzzleHttp\json_encode;

/**
 * Class Client
 * @package BG\Ares
 */
class Client
{

    const URL_IC = 'https://ares.gov.cz/ekonomicke-subjekty-v-be/rest/ekonomicke-subjekty/';
    /**
     * API endpoint for fetching data by IC
     * @see http://wwwinfo.mfcr.cz/ares/ares_xml_basic.html.cz
     */
    const URL_SEARCH = 'https://ares.gov.cz/ekonomicke-subjekty-v-be/rest/ekonomicke-subjekty/vyhledat';

    /** @var \GuzzleHttp\Client */
    protected $client;

    public function __construct()
    {
        $this->client = new \GuzzleHttp\Client();
    }

    /**
     * @param array $query (obchodniJmeno => ?, adresa => ?, ico => ?)
     * @param int $start
     * @param int $pocet
     * @throws AresException
     * @return Record[]|null
     */
    public function findByQuery(array $query, $start = 0, $pocet = 10)
    {
        $qry = [];
        if($query["obchodniJmeno"] ?? false) $qry["obchodniJmeno"] = $query["obchodniJmeno"];
        if($query["adresa"] ?? false) $qry["sidlo"]["textovaAdresa"] = $query["adresa"];
        if($query["ico"] ?? false) $qry["ico"] = $query["ico"];

        /** @var Response $res */
        $res = $this->client->post(self::URL_SEARCH, [
            "headers" => [
                "Content-Type" => "application/json",
            ],
            "body" => json_encode(array_merge([
                "start" => $start,
                "pocet" => $pocet,
            ], $qry))
        ]);

        if ($res->getStatusCode() !== 200) throw new AresException("Cannot fetch data! HTTP CODE " . $res->getStatusCode());

        $jsonData = json_decode($res->getBody()->getContents(), true);
        return array_map([$this, "recordFromResponse"], $jsonData["ekonomickeSubjekty"]);
    }

    /**
     * @param string[]|null $ICs
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
        $res = $this->findByQuery($query, 0, 1);
        return $res[0] ?? null;
    }

    /**
     * @param string $ic
     * @return Record|null
     */
    public function findOneByIC(string $ic)
    {
        $res = $this->client->get(self::URL_IC . $ic);
        if ($res->getStatusCode() !== 200) throw new AresException("Cannot fetch data! HTTP CODE " . $res->getStatusCode());

        $jsonData = json_decode($res->getBody()->getContents(), true);
        print_r($jsonData);
        return $this->recordFromResponse($jsonData);
    }

    protected function recordFromResponse(array $res)
    {
        $cisloOrientacni = $res['sidlo']['cisloOrientacni'] ?? null;
        if($cisloOrientacni){ $cisloOrientacni .= $res['sidlo']['cisloOrientacniPismeno'] ?? "";}

        return new Record(
            $res['obchodniJmeno'],
            $res['sidlo']['nazevUlice'] ?? $res['sidlo']['nazevCastiObce'] ?? "",
            (string)$res['sidlo']['cisloDomovni'],
            $cisloOrientacni,
            $res['sidlo']['nazevObce'],
            $res['sidlo']['psc'],
            $res['ico'],
            $res['dic'] ?? null,
        );
    }

}