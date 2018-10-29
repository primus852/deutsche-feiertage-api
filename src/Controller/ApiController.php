<?php

namespace App\Controller;

use App\Utils\Api;
use App\Utils\ApiException;
use primus852\ShortResponse\ShortResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController
{
    /**
     * @Route("/api/{search_year}", name="api", defaults={"search_year"="0"}, methods={"POST"})
     * @param Request $request
     * @param string $search_year
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function index(Request $request, string $search_year)
    {

        /**
         * Get the year we are looking for
         */
        $year = $search_year === "0" ? (new \DateTime())->format('Y') : $search_year;

        /**
         * Init the Api
         */
        try{
            $api = new Api($this->getDoctrine()->getManager(), $request);
        }catch (ApiException $e){
            return ShortResponse::exception('Initialization failed, '.$e->getMessage().'');
        }


        /**
         * Do the Request
         */
        try{
            $data = $api->by_year($year);
        }catch (ApiException $e){
            return ShortResponse::exception('Query failed, please try again shortly ('.$e->getMessage().')');
        }

        return ShortResponse::success('Data loaded',$data);
    }
}
