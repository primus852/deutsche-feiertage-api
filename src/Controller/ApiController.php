<?php

namespace App\Controller;

use App\Utils\Api;
use App\Utils\ApiException;
use primus852\ShortResponse\ShortResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController
{
    /**
     * @Route("/api/v1/{date}", name="apiV1", defaults={"date"="0"}, methods={"POST"})
     * @param Request $request
     * @param string $date
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function index(Request $request, string $date)
    {

        /**
         * Init the Api
         */
        try {
            $api = new Api($this->getDoctrine()->getManager(), $request);
        } catch (ApiException $e) {
            return ShortResponse::exception('Initialization failed, ' . $e->getMessage() . '');
        }

        /**
         * Get the endpoint by the sent date
         */
        $endpoint = $api->parse_date($date);
        if (!$endpoint) {
            return ShortResponse::error('Invalid Date');
        }

        /**
         * Do the Request
         */
        try {
            $data = $api->$endpoint($date);
        } catch (\Exception $e) {
            return ShortResponse::exception('Query failed, please try again', $e->getMessage());
        }

        /**
         * Return as JSON
         */
        return new JsonResponse($data);
    }
}
