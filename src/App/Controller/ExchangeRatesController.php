<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\Nbp;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;

class ExchangeRatesController extends AbstractController
{

    /**
     * Validates given string as date
     * @param string $date
     * @return bool
     */
    private function validateDate(string $date): bool
    {
        $res = false;
        try {
            if (\DateTime::createFromFormat('Y-m-d', $date)) {
                $res = true;
            }
        }
        catch (\ValueError $e) {}

        return $res;
    }

    /**
     *
     * @param Request $request
     * @param Nbp $nbp
     * @return Response
     */
    public function getRates(Request $request, Nbp $nbp): Response
    {
        $date = $request->get('date');
        if ($date && !$this->validateDate($date)) {
            return new Response(
                json_encode([
                    'message' => 'Invalid date'
                ]),
                Response::HTTP_BAD_REQUEST,
                ['Content-type' => 'application/json']
            );
        }
        try {
            return new Response(
                json_encode([
                    'rates' => $nbp->getCurrencies($date)
                ]),
                Response::HTTP_OK,
                ['Content-type' => 'application/json']
            );
        }
        catch (ExceptionInterface $e) {
            return new Response(
                json_encode([
                    'message' => 'Can not retrieve data'
                ]),
                Response::HTTP_SERVICE_UNAVAILABLE,
                ['Content-type' => 'application/json']
            );
        }
    }

}
