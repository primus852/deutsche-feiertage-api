<?php

namespace App\Utils;


class ApiException extends \Exception
{

    /**
     * ApiException constructor.
     * @param string $message
     * @param int $code
     * @param \Exception|null $previous
     */
    public function __construct(string $message, int $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return string
     */
    public function __toString()
    {

        $trace = explode("\n", $this->getTraceAsString());

        array_pop($trace); // remove call to this method
        $length = count($trace);
        $result = array();

        for ($i = 0; $i < $length; $i++) {
            $result[] = substr($trace[$i], strpos($trace[$i], ' '));
        }

        return 'Caller:'.$result[0].', Line '.$this->line.': '.$this->getMessage();
    }


}