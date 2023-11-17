<?php

namespace Unit\Service;

use App\Service\Nbp;
use App\Service\NbpTable;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class NbpTest extends TestCase
{
    const DATE = '2023-11-13';

    public function testGetCurrencies(): void
    {
        $parameterBagMock = $this->createMock(ParameterBagInterface::class);
        $parameterBagMock->expects($this->once())
            ->method('get')
            ->with($this->equalTo('app.currencies'))
            ->willReturn([]);

        $nbpTableMock = $this->createMock(NbpTable::class);
        $nbpTableMock->expects($this->once())
            ->method('getNbpTable')
            ->with($this->equalTo(self::DATE))
            ->willReturn(['rates' => []]);

        $nbp = new Nbp($parameterBagMock, $nbpTableMock);
        $nbp->getCurrencies(self::DATE);
    }

    public function testGetFilteredCurrencies(): void
    {
        $parameterBagMock = $this->createMock(ParameterBagInterface::class);
        $parameterBagMock->expects($this->once())
            ->method('get')
            ->with($this->equalTo('app.currencies'))
            ->willReturn([
                'curr1' => ['buy' => -0.1, 'sell' => 0.1],
                'curr2' => ['buy' => -0.1, 'sell' => 0.1]
            ]);

        $nbpTableMock = $this->createMock(NbpTable::class);
        $nbpTableMock->expects($this->once())
            ->method('getNbpTable')
            ->with($this->equalTo(self::DATE))
            ->willReturn([
                'date' => self::DATE,
                'rates' => [
                    [
                        'code' => 'curr1',
                        'currency' => 'currency 1',
                        'mid' => 9.99
                    ],
                    [
                        'code' => 'curr2',
                        'currency' => 'currency 2',
                        'mid' => 19.99
                    ],
                    [
                        'code' => 'curr3',
                        'currency' => 'currency 3',
                        'mid' => 0.99
                    ],
                ]
            ]);

        $nbp = new Nbp($parameterBagMock, $nbpTableMock);
        $result = $nbp->getCurrencies(self::DATE);
        $this->assertArrayHasKey('rates', $result);
        $this->assertCount(2, $result['rates']);
        $this->assertArrayHasKey('curr1', $result['rates']);
        $this->assertArrayHasKey('curr2', $result['rates']);
    }

}