<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/v2', name: 'app_api_v2')]
class ApiV2Controller extends AbstractController
{
    #[Route('/{date}', name: 'app_api_v2_date')]
    public function date(): Response
    {
        return $this->render('api_v1/index.html.twig', [
            'controller_name' => 'ApiV1Controller',
        ]);
    }
}
