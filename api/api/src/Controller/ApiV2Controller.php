<?php

namespace App\Controller;

use App\Repository\HolidayRepository;
use App\Service\ApiV2Service;
use App\Util\ApiResponse;
use App\Util\ProjectException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\InputBag;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/v2', name: 'app_api_v2')]
class ApiV2Controller extends AbstractController
{
    private ApiV2Service $apiV2Service;
    private InputBag $_queryParams;

    /**
     * @param RequestStack $requestStack
     * @param HolidayRepository $holidayRepository
     * @throws ProjectException
     */
    public function __construct(private readonly RequestStack $requestStack, private HolidayRepository $holidayRepository)
    {
        $this->apiV2Service = new ApiV2Service($this->requestStack, $this->holidayRepository);
        $this->_queryParams = $this->requestStack->getCurrentRequest()->query;
    }

    #[Route('/by-date/{date}', name: 'app_api_v2_date')]
    public function singleDate(string $date): Response
    {

        try {
            $data = $this->apiV2Service->getSingleDate($date, $this->_queryParams->get('includeInfo') === 'true');
        } catch (ProjectException $e) {
            return ApiResponse::exception($e);
        }

        return ApiResponse::success('HOLIDAY_LOADED', 200, $data);
    }

    #[Route('/by-state-date/{state}/{date}', name: 'app_api_v2_state_date')]
    public function singleDateByState(string $state, string $date): Response
    {

        try {
            $data = $this->apiV2Service->getSingleDateByState($state, $date);
        } catch (ProjectException $e) {
            return ApiResponse::exception($e);
        }

        return ApiResponse::success('HOLIDAY_LOADED', 200, $data);
    }
}
