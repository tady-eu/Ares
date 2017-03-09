<?php

namespace BG\Ares\Parsers;

use BG\Ares\AresException;

/**
 * Class StandardIdParser
 * @package BG\Ares\Parsers
 *
 * Standard endpoint does't provide DIC, so we fetch only IČs and let BasicParser fetch other data
 */
class StandardIdParser
{
    /**
     * @param \SimpleXMLElement $xml
     * @throws AresException
     * @see http://wwwinfo.mfcr.cz/cgi-bin/ares/darv_std.cgi?obchodni_firma=ASSECO for expected XML
     * @return int[]|null
     */
    public static function parseXml(\SimpleXMLElement $xml)
    {
        $namespaces = $xml->getDocNamespaces();
        $response = $xml->children($namespaces['are'])->Odpoved;

        $recordsCount = $response->Pocet_zaznamu;
//        No records found
        if ($recordsCount == 0) return null;

        $ICs = [];
        foreach ($response->Zaznam as $record) {
            /* @var $record \SimpleXMLElement */

            $ICs[] = (int)$record->ICO;
        }

        return !empty($ICs) ? $ICs : null;
    }

}