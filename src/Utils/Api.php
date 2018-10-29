<?php

namespace App\Utils;


use App\Entity\Holiday;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;

class Api
{

    private $em;

    /**
     * Api constructor.
     * @param ObjectManager $em
     * @param Request $request
     * @throws ApiException
     */
    public function __construct(ObjectManager $em, Request $request)
    {
        if($request->headers->get('X-DFA-Token') === null){
            throw new ApiException('Missing Token');
        }

        if($request->headers->get('X-DFA-Token') !== 'dfa'){
            throw new ApiException('Invalid Token');
        }

        $this->em = $em;
    }

    /**
     * Api destructor
     */
    public function __destruct()
    {
        $this->em = null;
    }

    /**
     * @param int $year
     * @return array
     * @throws ApiException
     */
    public function by_year(int $year)
    {

        /**
         * Allow range from 1950 - 2050
         */
        if (!preg_match('/^(19[5-9]\d|20[0-4]\d|2050)$/', $year)) {
            throw new ApiException('Please select a date between 1950 and 2050');
        }

        /**
         * Gather all holidays that have this year or null as 'holidayYear'
         */
        $holidays = $this->em->getRepository(Holiday::class)->findBy(array(
            'holidayYear' => array(
                null,
                $year
            )
        ));

        /**
         * Show if we have something
         */
        $result = $holidays === null ? 'error' : 'success';
        $message = $holidays === null ? 'Could not find any holidays' : false;

        /**
         * Fill our data array
         */
        $data = array();
        foreach ($holidays as $holiday) {

            /**
             * Check which states are included
             */
            $regions = array(
                'bw' => $holiday->getBw(),
                'bay' => $holiday->getBay(),
                'be' => $holiday->getBe(),
                'bb' => $holiday->getBb(),
                'hb' => $holiday->getHb(),
                'hh' => $holiday->getHh(),
                'he' => $holiday->getHe(),
                'mv' => $holiday->getMv(),
                'ni' => $holiday->getNi(),
                'nw' => $holiday->getNw(),
                'rp' => $holiday->getRp(),
                'sl' => $holiday->getSl(),
                'sn' => $holiday->getSn(),
                'st' => $holiday->getSt(),
                'sh' => $holiday->getSh(),
                'th' => $holiday->getTh(),
            );

            /**
             * Construct the date
             */
            $date = \DateTime::createFromFormat('j.n.Y', $holiday->getHolidayDay() . '.' . $holiday->getHolidayMonth() . '.' . $year);
            if($date === false){
                throw new ApiException('Invalid Date: '.$holiday->getHolidayDay() . '.' . $holiday->getHolidayMonth() . '.' . $year);
            }

            /**
             * Put it all together
             */
            $data[] = array(
                'holiday' => array(
                    'date' => $date->format('Y-m-d'),
                    'name' => $holiday->getHolidayName(),
                    'regions' => $regions,
                    'all_states' => $holiday->getBundesweit(),
                ),
            );
        }

        /**
         * Final Response
         */
        return array(
            'result' => $result,
            'message' => $message,
            'holidays' => $data
        );
    }

}