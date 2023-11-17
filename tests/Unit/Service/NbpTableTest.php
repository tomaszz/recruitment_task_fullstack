<?php

namespace Unit\Service;

use App\Service\NbpTable;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class NbpTableTest extends TestCase
{
    const DATE = '2023-11-13';

    public function testMakingGETRequest(): void
    {
        $responseMock = $this->createMock(ResponseInterface::class);
        $responseMock->expects($this->once())
            ->method('toArray')
            ->willReturn([
                [
                    'effectiveDate' => self::DATE,
                    'rates' => []
                ]
            ]);

        $httpClientMock = $this->createMock(HttpClientInterface::class);
        $httpClientMock->expects($this->once())
            ->method('request')
            ->with($this->equalTo('GET'))
            ->willReturn($responseMock);

        $nbpTable = new NbpTable($httpClientMock);
        $result = $nbpTable->getNbpTable();
        $this->assertArrayHasKey('date', $result);
        $this->assertArrayHasKey('rates', $result);
    }

    public function testGetNbpTableHandlesClientException(): void
    {
        $responseMock = $this->createMock(ResponseInterface::class);

        $clientExceptionMock = $this->getMockBuilder(ClientExceptionInterface::class)
            ->setConstructorArgs([$responseMock])
            ->disableOriginalConstructor()
            ->getMock();

        $httpClientMock = $this->createMock(HttpClientInterface::class);
        $httpClientMock->expects($this->once())
            ->method('request')
            ->with($this->equalTo('GET'))
            ->willThrowException($clientExceptionMock);

        $nbpTable = new NbpTable($httpClientMock);
        $result = $nbpTable->getNbpTable();
        $this->assertArrayHasKey('date', $result);
        $this->assertArrayHasKey('rates', $result);
    }

    public function testGetNbpTableBypassTransportException(): void
    {
        $responseMock = $this->createMock(ResponseInterface::class);

        $transportExceptionMock = $this->getMockBuilder(TransportExceptionInterface::class)
            ->setConstructorArgs([$responseMock])
            ->disableOriginalConstructor()
            ->getMock();

        $httpClientMock = $this->createMock(HttpClientInterface::class);
        $httpClientMock->expects($this->once())
            ->method('request')
            ->with($this->equalTo('GET'))
            ->willThrowException($transportExceptionMock);

        $nbpTable = new NbpTable($httpClientMock);

        $this->expectException(TransportExceptionInterface::class);
        $result = $nbpTable->getNbpTable();
    }
}