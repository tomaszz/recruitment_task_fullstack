<?php

namespace Unit\Entity;

use App\Entity\Currency;
use PHPUnit\Framework\TestCase;

class CurrencyTest extends TestCase
{
    /**
     * @dataProvider currencyProvider
     */
    public function testCurrencyWithBuyAndSellSpread(array $params, array $expectedArray): void
    {
        $currency = new Currency(...$params);
        $this->assertEquals($expectedArray, $currency->toArray());
    }

    public function currencyProvider(): array
    {
        return [
            [
                ['USD', 'dolar', 9.99, -0.1, 0.1],
                ['code' => 'USD', 'name' => 'dolar', 'nbp' => 9.99, 'buy' => 9.89, 'sell' => 10.09]
            ],
            [
                ['EUR', 'euro', 15.66, -0.11, 0.11],
                ['code' => 'EUR', 'name' => 'euro', 'nbp' => 15.66, 'buy' => 15.55, 'sell' => 15.77]
            ],
            [
                ['CZK', 'korona', 1.58, null, 0.11],
                ['code' => 'CZK', 'name' => 'korona', 'nbp' => 1.58, 'buy' => null, 'sell' => 1.69]
            ],
            [
                ['BRL', 'real', 1.58, -0.11, null],
                ['code' => 'BRL', 'name' => 'real', 'nbp' => 1.58, 'buy' => 1.47, 'sell' => null]
            ]
        ];
    }
}