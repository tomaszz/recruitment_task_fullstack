<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class NbpTable
{
    const API_URI = 'https://api.nbp.pl/api/exchangerates/tables/A/';

    /**
     * @var HttpClientInterface
     */
    private $httpClient;

    /**
     * @param HttpClientInterface $httpClient
     */
    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * Gets exchange rates "Table A" from API
     *
     * @param string|null $date
     * @return array
     *
     * @throws ExceptionInterface
     */
    public function getNbpTable(?string $date = null): array
    {
        $result = [
            'date' => $date,
            'rates' => []
        ];
        try {
            $response = $this->httpClient->request(
                'GET',
                self::API_URI . ($date ? urlencode($date) : ''),
                ['query' => ['format' => 'json']]
            );
            $responseArray = $response->toArray();
            if ($responseArray && (count($responseArray) > 0)) {
                $table = array_pop($responseArray);
                if (isset($table['rates'])) {
                    $result = [
                        'date' => $table['effectiveDate'],
                        'rates' => $table['rates']
                    ];
                }
            }
        } catch (ClientExceptionInterface $e) {
            //40x returned - no data for date
        }

        return $result;
    }

}