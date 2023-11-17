<?php

namespace App\Entity;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class Currency
{
    /**
     * @var string
     */
    private $code;

    /**
     * @var string
     */
    private $currency;

    /**
     * @var float
     */
    private $nbpRate;

    /**
     * @var float|null
     */
    private $buySpread;

    /**
     * @var float|null
     */
    private $sellSpread;

    /**
     * @param string $code
     * @param string $currency
     * @param float $nbpRate
     * @param float|null $buySpread
     * @param float|null $sellSpread
     */
    public function __construct(string $code, string $currency, float $nbpRate, ?float $buySpread, ?float $sellSpread)
    {
        $this->code = $code;
        $this->currency = $currency;
        $this->nbpRate = $nbpRate;
        $this->buySpread = $buySpread;
        $this->sellSpread = $sellSpread;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'code' => $this->code,
            'name' => $this->currency,
            'nbp' => round($this->nbpRate, 4),
            'buy' => $this->buySpread ? round($this->nbpRate + $this->buySpread, 4) : null,
            'sell' => $this->sellSpread ? round($this->nbpRate + $this->sellSpread, 4) : null,
        ];
    }
}