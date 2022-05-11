<?php


namespace App\Util;


use Exception;
use JetBrains\PhpStorm\Pure;
use Throwable;

class ProjectException extends Exception
{
    private array $details;

    public function __construct($message, array $details = array(), $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->setDetails($details);
    }

    public function setDetails(array $data)
    {
        $this->details = $data;
    }

    /**
     * @return array
     */
    #[Pure]
    public function getDetails(): array
    {
        if (getenv('APP_ENV') !== 'prod') {
            return $this->details;
        }

        return array();
    }

}
