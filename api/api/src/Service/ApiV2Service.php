<?php

namespace App\Service;

use App\Entity\Holiday;
use App\Entity\HolidayComment;
use App\Enum\GermanState;
use App\Repository\HolidayRepository;
use App\Util\ProjectException;
use App\Util\Util;
use DateTime;
use Exception;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use ValueError;

class ApiV2Service
{

    /**
     * @param RequestStack $requestStack
     * @param HolidayRepository $holidayRepository
     * @throws ProjectException
     */
    public function __construct(private readonly RequestStack $requestStack, private HolidayRepository $holidayRepository)
    {

        if (!$this->_validateCredentials($this->requestStack->getCurrentRequest())) {
            throw new ProjectException('UNAUTHORIZED', array(), 401);
        }

    }

    /**
     * @param string $date
     * @param bool $includeDetails
     * @return array
     * @throws ProjectException
     */
    #[ArrayShape(['isHoliday' => "bool", 'holiday' => "array", 'isBundesweit' => "bool|null", 'info' => "array"])]
    public function getSingleDate(string $date, bool $includeDetails = false): array
    {

        $split_date = $this->_split_date($date);

        $query = $this->holidayRepository->findBy(array(
            'holidayDay' => (int)$split_date->format('d'),
            'holidayMonth' => (int)$split_date->format('m'),
        ));

        $holiday = $this->_handleQuery($query, $split_date);

        if ($holiday === false) {
            return Util::notAHoliday();
        }


        $result = array(
            'isHoliday' => true,
            'holiday' => array(
                'name' => $holiday->getHolidayName(),
                'date' => $date
            ),
            'isBundesweit' => $holiday->getIsBundesweit()
        );

        if ($includeDetails) {
            $result['info'] = $this->_getDetails($holiday);
        }


        return $result;
    }

    /**
     * @param string $stateValue
     * @param string $date
     * @return array
     * @throws ProjectException
     */
    public function getSingleDateByState(string $stateValue, string $date): array
    {

        try {
            $state = GermanState::from($stateValue);
        } catch (ValueError $e) {
            throw new ProjectException('INVALID_STATE', array(), 400);
        }

        $split_date = $this->_split_date($date);

        $query = $this->holidayRepository->findBy(array(
            'holidayDay' => (int)$split_date->format('d'),
            'holidayMonth' => (int)$split_date->format('m'),
            'is' . ucfirst($state->value) => true
        ));

        $holiday = $this->_handleQuery($query, $split_date);

        if ($holiday === false) {
            return Util::notAHoliday($state);
        }

        return array(
            'state' => $state->value,
            'isHoliday' => true,
            'holiday' => array(
                'name' => $holiday->getHolidayName(),
                'date' => $date
            ),
            'isBundesweit' => $holiday->getIsBundesweit()
        );

    }

    /**
     * @param array|null $holidays
     * @param DateTime $date
     * @return bool|Holiday
     */
    private function _handleQuery(array|null $holidays, DateTime $date): bool|Holiday
    {

        if ($holidays === null) {
            return false;
        }

        $found = null;
        foreach ($holidays as $holiday) {
            if ($holiday->getHolidayYear() === null) {
                $found = $holiday;
                break;
            }

            if ($holiday->getHolidayYear() === (int)$date->format('Y')) {
                $found = $holiday;
                break;
            }
        }

        if ($found === null) {
            return false;
        }

        return $found;
    }

    /**
     * @param string $date
     * @return DateTime
     * @throws ProjectException
     */
    private function _split_date(string $date): DateTime
    {
        try {
            return new DateTime($date);
        } catch (Exception $e) {
            throw new ProjectException('INVALID_DATE', array(), 400);
        }
    }

    /**
     * @param Request $request
     * @return bool
     */
    private function _validateCredentials(Request $request): bool
    {
        $authorizationHeader = $request->headers->get('Authorization');
        $token = substr($authorizationHeader, 7);

        return Util::isJwtValid($token, $_ENV['JWT_SIGNATURE']);

    }

    /**
     * @param Holiday $holiday
     * @return array
     */
    #[ArrayShape(['laender' => "array", 'comments' => "array"])]
    private function _getDetails(Holiday $holiday): array
    {
        $comments = array();

        if ($holiday->getHolidayComments()->count() > 0) {
            /* @var HolidayComment $comment */
            foreach ($holiday->getHolidayComments() as $comment) {
                if (!in_array($comment->getComment(), $comments)) {
                    $comments[] = $comment->getComment();
                }
            }
        }

        return array(
            'laender' => array(
                'bw' => $holiday->getIsBw(),
                'bay' => $holiday->getIsBay(),
                'be' => $holiday->getIsBe(),
                'bb' => $holiday->getIsBb(),
                'hb' => $holiday->getIsHb(),
                'hh' => $holiday->getIsHh(),
                'he' => $holiday->getIsHe(),
                'mv' => $holiday->getIsMv(),
                'ni' => $holiday->getIsNi(),
                'nw' => $holiday->getIsNw(),
                'rp' => $holiday->getIsRp(),
                'sl' => $holiday->getIsSl(),
                'sn' => $holiday->getIsSn(),
                'st' => $holiday->getIsSt(),
                'sh' => $holiday->getIsSh(),
                'th' => $holiday->getIsTh(),
            ),
            'comments' => $comments
        );
    }

}
