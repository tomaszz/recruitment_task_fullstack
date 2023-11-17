<?php

namespace Integration\ExchangeRatesCheck;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class ExchangeRatesCheckTest extends WebTestCase
{

    public function testAPI(): void
    {
        $client = static::createClient();

        // test e.g. the profile page
        $client->request('GET', '/api/get-exchange-rates');
        $this->assertResponseIsSuccessful();
        $response = $client->getResponse();
        $this->assertJson($response->getContent());
        $responseData = json_decode($response->getContent(), TRUE);
        $this->assertArrayHasKey('rates', $responseData);
        $this->assertArrayHasKey('date', $responseData['rates']);
        $this->assertArrayHasKey('rates', $responseData['rates']);
    }

    public function testAPIForProperDate(): void
    {
        $client = static::createClient();

        // test e.g. the profile page
        $client->request('GET', '/api/get-exchange-rates?date=2023-01-13');
        $this->assertResponseIsSuccessful();
        $response = $client->getResponse();
        $this->assertJson($response->getContent());
        $responseData = json_decode($response->getContent(), TRUE);
        $this->assertArrayHasKey('rates', $responseData);
        $this->assertArrayHasKey('date', $responseData['rates']);
        $this->assertArrayHasKey('rates', $responseData['rates']);
    }

    /**
     * Checks if error code is set for request with wrong date
     *
     * @return void
     */
    public function testAPIWithWrongDate(): void
    {
        $client = static::createClient();

        // test e.g. the profile page
        $client->request('GET', '/api/get-exchange-rates?date=2023-11-ab');
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $response = $client->getResponse();
        $this->assertJson($response->getContent());
        $responseData = json_decode($response->getContent(), TRUE);
        $this->assertArrayHasKey('message', $responseData);
    }

}