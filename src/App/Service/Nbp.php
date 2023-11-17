<?php

namespace App\Service;

use App\Entity\Currency;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class Nbp
{

    /**
     * @var ParameterBagInterface
     */
    private $params;

    /**
     * @var NbpTable
     */
    private $nbpTable;

    /**
     * @param ParameterBagInterface $params
     */
    public function __construct(ParameterBagInterface $params, NbpTable $nbpTable)
    {
        $this->params = $params;
        $this->nbpTable = $nbpTable;
    }


    /**
     * Retrieves app parameter "app.currencies"
     * @return array
     */
    private function getAppCurrencies(): array
    {
        return $this->params->get('app.currencies');
    }

    /**
     * Returns filtered array of exchange rates based on app configuration (app.currencies)
     * @param array $currencies Input array with exchange rates
     * @return array
     */
    private function filterCurrencies(array $currencies): array
    {
        $filteredCurrencies = [];

        $appCurrencies = $this->getAppCurrencies();
        foreach ($currencies as  $currency) {
            if (isset($appCurrencies[$currency['code']])) {
                $currencyEntity = new Currency(
                    $currency['code'],
                    $currency['currency'],
                    $currency['mid'],
                    $appCurrencies[$currency['code']]['buy'],
                    $appCurrencies[$currency['code']]['sell']
                );
                $filteredCurrencies[$currency['code']] = $currencyEntity->toArray();
            }
        }

        return $filteredCurrencies;
    }

    /**
     * Returns exchange rates for $date or current rates if $date is null
     *
     * @param string|null $date
     * @return array
     * @throws ExceptionInterface
     */
    public function getCurrencies(?string $date): array
    {
        $table = $this->nbpTable->getNbpTable($date);
        $table['rates'] = $this->filterCurrencies($table['rates']);

        return $table;
    }
}