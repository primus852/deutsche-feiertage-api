<?php
/**
 * Created by PhpStorm.
 * User: torsten
 * Date: 13.12.2018
 * Time: 10:56
 */

namespace App\Utils;


class Alexa
{

    private $skill_id;

    /**
     * Alexa constructor.
     * @param string $skill_id
     * @throws AlexaException
     */
    public function __construct(string $skill_id)
    {
        if($skill_id === null || $skill_id === ''){
            throw new AlexaException('Empty Skill ID');
        }

        $this->skill_id = $skill_id;
    }

    /**
     * @param array $request
     * @throws AlexaException
     */
    public function verify(array $request)
    {
        if($this->skill_id !== $request['session']['application']['applicationId']){
            throw new AlexaException('Invalid Skill ID || '.__LINE__);
        }
    }
}