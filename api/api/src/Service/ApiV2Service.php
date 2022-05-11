<?php

namespace App\Service;

use App\Util\ProjectException;
use Symfony\Component\HttpFoundation\RequestStack;

class ApiV2Service
{

    public function __construct(private RequestStack $requestStack)
    {

        if ($this->requestStack->getCurrentRequest()->headers->get('X-DFA-Token') === null) {
            throw new ProjectException('MISSING_TOKEN');
        }

        if ($this->requestStack->getCurrentRequest()->headers->get('X-DFA-Token') !== 'dfa') {
            throw new ProjectException('INVALID_TOKEN');
        }

    }

}
