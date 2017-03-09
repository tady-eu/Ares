<?php
/**
 * @author Jaromír Navara [BG] <web@bagricek.cz>
 * AresSubject.php
 * 8.3.17 23:49
 */

declare(strict_types=1);

namespace BG\Ares;

class Record{

    /** @var string */
    protected $firma;

    /** @var string */
    protected $ulice;

    /** @var string */
    protected $mesto;

    /** @var int */
    protected $psc;

    /** @var int */
    protected $ic;

    /** @var string */
    protected $dic;

    /**
     * AresData constructor.
     * @param string $firma
     * @param string $ulice
     * @param string $mesto
     * @param int $psc
     * @param int $ic
     * @param string $dic
     */
    public function __construct(string $firma, string $ulice, string $mesto, int $psc, int $ic, string $dic)
    {
        $this->firma = $firma;
        $this->ulice = $ulice;
        $this->mesto = $mesto;
        $this->psc = $psc;
        $this->ic = $ic;
        $this->dic = $dic;
    }

    /**
     * @return string
     */
    public function getFirma(): string
    {
        return $this->firma;
    }

    /**
     * @return string
     */
    public function getUlice(): string
    {
        return $this->ulice;
    }

    /**
     * @return string
     */
    public function getMesto(): string
    {
        return $this->mesto;
    }

    /**
     * @return int
     */
    public function getPsc(): int
    {
        return $this->psc;
    }

    /**
     * @return int
     */
    public function getIco(): int
    {
        return $this->ic;
    }

    /**
     * @return string
     */
    public function getDic(): string
    {
        return $this->dic;
    }

}