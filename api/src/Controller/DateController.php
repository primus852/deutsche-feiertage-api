<?php

namespace App\Controller;

use App\Service\DateService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/public/by-date')]
final class DateController extends AbstractController
{

    public function __construct(
        private readonly DateService $dateService
    )
    {
    }

    #[Route('/{date}', name: 'api_by_date_date', methods: ['GET'])]
    public function byDate(string $date): JsonResponse
    {
        return $this->dateService->getHolidayForDate($date);
    }
}
