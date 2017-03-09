<?php
/**
 * @author Jaromír Navara [BG] <web@bagricek.cz>
 * AresFetch.php
 * 8.3.17 23:49
 */

declare(strict_types=1);

namespace BG\Ares;

use GuzzleHttp\Psr7\Response;

/**
 * Class Client
 * @package BG\Ares
 */
class Client{

    /** Entry API URL */
    const BASE_URL = 'http://wwwinfo.mfcr.cz/cgi-bin/ares/darv_std.cgi';

    const DEFAULT_PARAMETERS = [
        "czk" => "utf"
    ];

    /** @var \GuzzleHttp\Client */
    protected $client;

    public function __construct()
    {
        $this->client = new \GuzzleHttp\Client([
            "base_uri" => self::BASE_URL
        ]);
    }

    /**
     * @param array $query Possible parameters of query as follows:
     * @see http://wwwinfo.mfcr.cz/ares/ares_xml_standard.html.cz For allowed parameters
     * @throws AresException
     * @return Record[]|null
     */
    public function findByQuery(array $query){
        /** @var Response $res */
        $res = $this->client->get(null, ["query" => array_merge(self::DEFAULT_PARAMETERS, $query)]);

        if($res->getStatusCode() !== 200) throw new AresException("Cannot fetch data! HTTP CODE " . $res->getStatusCode());

        $xmlElem = new \SimpleXMLElement((string)$res->getBody());
        return $this->parseXml($xmlElem);
    }

    /**
     * @param array $query
     * @see fetchByQuery method for query params
     * @return Record|null
     */
    public function findOneByQuery(array $query){
        $res = $this->findByQuery($query);
        return $res !== null ? $res[0] : null;
    }

    /**
     * @param int $ic
     * @return Record|null
     */
    public function findOneByIC(int $ic){
        return $this->findOneByQuery(["ico" => intval($ic)]);
    }

    /**
     * @param \SimpleXMLElement $xml
     * @throws AresException
     * @see http://wwwinfo.mfcr.cz/cgi-bin/ares/darv_std.cgi?obchodni_firma=ASSECO for expected XML
     * @return Record[]|null
     */
    protected function parseXml(\SimpleXMLElement $xml){
        $namespaces = $xml->getDocNamespaces();
        $response = $xml->children($namespaces['are'])->Odpoved;

        $recordsCount = $response->Pocet_zaznamu;
//        No records found
        if($recordsCount == 0) return null;

        $subjects = [];
        foreach ($response->Zaznam as $record){
            /* @var $record \SimpleXMLElement */

//            Get address
            if($record->Identifikace->Adresa_ARES){
                $address = $record->Identifikace->Adresa_ARES->children($namespaces["dtt"]);
            }elseif ($record->Identifikace->Osoba){
                $address = $record->Identifikace->Osoba->children($namespaces["dtt"])->Bydliste;
            }

            if(!isset($address)) throw new AresException("Cannot parse address out of XML");

            $subjects[] = new Record(
                (string)$record->Obchodni_firma,
                "{$address->Nazev_ulice} {$address->Cislo_domovni}" . isset($address->Cislo_orientacni) ? "/{$address->Cislo_orientacni}" : "",
                (string)$address->Nazev_obce,
                (int)$address->PSC,
                (int)$record->ICO,
                (string)$address->DIC);
        }

        return !empty($subjects) ? $subjects : null;
    }
}