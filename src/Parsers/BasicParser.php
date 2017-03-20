<?php
/**
 * @author Jaromír Navara [BG] <web@bagricek.cz>
 * BasicParser.php
 * 9.3.17 7:43
 */

namespace BG\Ares\Parsers;

use BG\Ares\Record;

/**
 * Class BasicParser
 * @package BG\Ares\Parsers
 */
class BasicParser
{

    /**
     * @param \SimpleXMLElement $xml
     * @return Record[]|null
     */
    public static function parseXml(\SimpleXMLElement $xml)
    {
        $namespaces = $xml->getDocNamespaces();
        $odpovedi = $xml->children($namespaces['are'])->Odpoved;

        $subjects = [];
        foreach ($odpovedi as $odpoved) {
            $data = $odpoved->children($namespaces["D"])->VBAS;
            $subjects[] = new Record(
                (string)$data->OF,
                (string)$data->AA->NU,
                (string)$data->AA->CD,
                $data->AA->CO ? (string)$data->AA->CO : NULL,
                (string)$data->AA->N,
                (int)$data->AA->PSC,
                (int)$data->ICO,
                $data->DIC ? (string)$data->DIC : null);
        }

        return !empty($subjects) ? $subjects : null;
    }
}