<?php

namespace Unit\Controller;

use App\Controller\ExchangeRatesController;
use App\Service\Nbp;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class ExchangeRatesControllerTest extends TestCase
{
    const DATE = '2023-11-13';
    const INCORRECT_DATE = 'this is not a date';

    public function testGetRatesWithIncorrectDate(): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->expects($this->once())
            ->method('get')
            ->with('date')
            ->willReturn(self::INCORRECT_DATE);

        $nbpMock = $this->createMock(Nbp::class);
        $nbpMock->expects($this->never())
            ->method('getCurrencies');

        $exchangeRatesController = new ExchangeRatesController();
        $result = $exchangeRatesController->getRates($requestMock, $nbpMock);
        $this->assertInstanceOf(Response::class, $result);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $result->getStatusCode());
    }

    public function testGetRatesWithCorrectDate(): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->expects($this->once())
            ->method('get')
            ->with('date')
            ->willReturn(self::DATE);

        $nbpMock = $this->createMock(Nbp::class);
        $nbpMock->expects($this->once())
            ->method('getCurrencies')
            ->with($this->equalTo(self::DATE))
            ->willReturn([]);

        $exchangeRatesController = new ExchangeRatesController();
        $result = $exchangeRatesController->getRates($requestMock, $nbpMock);
        $this->assertInstanceOf(Response::class, $result);
        $this->assertEquals(Response::HTTP_OK, $result->getStatusCode());
    }

    public function testGetRatesHandlesException(): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->expects($this->once())
            ->method('get')
            ->with('date')
            ->willReturn(self::DATE);

        $responseMock = $this->createMock(ResponseInterface::class);
        $transportExceptionMock = $this->getMockBuilder(TransportExceptionInterface::class)
            ->setConstructorArgs([$responseMock])
            ->disableOriginalConstructor()
            ->getMock();

        $nbpMock = $this->createMock(Nbp::class);
        $nbpMock->expects($this->once())
            ->method('getCurrencies')
            ->with($this->equalTo(self::DATE))
            ->willThrowException($transportExceptionMock);

        $exchangeRatesController = new ExchangeRatesController();
        $result = $exchangeRatesController->getRates($requestMock, $nbpMock);
        $this->assertInstanceOf(Response::class, $result);
        $this->assertEquals(Response::HTTP_SERVICE_UNAVAILABLE, $result->getStatusCode());
    }

}