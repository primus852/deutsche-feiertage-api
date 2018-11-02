<?php

namespace App\Utils;


use App\Entity\Holiday;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PropertyAccess\PropertyAccess;

class Api
{

    private $em;
    private $request;
    private $defaults;
    private $bundeslaender;

    /**
     * Api constructor.
     * @param ObjectManager $em
     * @param Request $request
     * @throws ApiException
     */
    public function __construct(ObjectManager $em, Request $request)
    {
        if ($request->headers->get('X-DFA-Token') === null) {
            throw new ApiException('Missing Token');
        }

        if ($request->headers->get('X-DFA-Token') !== 'dfa') {
            throw new ApiException('Invalid Token');
        }

        $this->defaults = array(
            'bundesweit' => false,
            'bundeslaender' => null,
            'short' => false,
            'info' => false,
        );

        $this->bundeslaender = array(
            'bw' => 'Baden-Württemberg',
            'bay' => 'Bayern',
            'be' => 'Berlin',
            'bb' => 'Brandenburg',
            'hb' => 'Bremen',
            'hh' => 'Hamburg',
            'he' => 'Hessen',
            'mv' => 'Mecklenburg-Vorpommern',
            'ni' => 'Niedersachsen',
            'nw' => 'Nordrhein-Westfalen',
            'rp' => 'Rheinland-Pfalz',
            'sl' => 'Saarland',
            'sn' => 'Sachsen',
            'st' => 'Sachsen-Anhalt',
            'sh' => 'Schleswig-Holstein',
            'th' => 'Thüringen',
        );

        $this->em = $em;
        $this->request = $request;
    }

    /**
     * Api destructor
     */
    public function __destruct()
    {
        $this->em = null;
    }

    /**
     * @param string $date
     * @return bool|string
     */
    public function parse_date(string $date)
    {

        /**
         * See if we have a year
         */
        $parsed = \DateTime::createFromFormat('Y', $date);
        if ($parsed !== false) {
            return 'by_year';
        }

        /**
         * See if we have a regular date
         */
        $parsed = \DateTime::createFromFormat('Y-m-d', $date);
        if ($parsed !== false) {
            return 'by_day';
        }

        return false;

    }

    /**
     * @param string $d
     * @return array
     * @throws ApiException
     */
    public function by_day(string $d)
    {

        $data = array();
        $addBl = '';
        $accessor = PropertyAccess::createPropertyAccessor();

        $day = \DateTime::createFromFormat('Y-m-d', $d);
        if ($day === false) {
            throw new ApiException('Invalid Date: ' . $d);
        }

        $opt = array(
            'holidayDay' => $day->format('j'),
            'holidayMonth' => $day->format('n'),
            'holidayYear' => array(
                null,
                $day->format('Y')
            )
        );

        /**
         * Parse additional params
         */
        try {
            self::parse_params(true);
        } catch (ApiException $e) {
            throw new ApiException($e);
        }

        $general = '';
        if ($this->defaults['bundesweit'] === true) {
            $opt['bundesweit'] = true;
        }

        /**
         * Gather all holidays that have this year or null as 'holidayYear'
         */
        $holiday = $this->em->getRepository(Holiday::class)->findOneBy($opt);

        /**
         * Check how many have this holiday
         */
        $countBl = 0;
        $inStates = array();
        if ($holiday !== null) {
            foreach ($this->bundeslaender as $key => $val) {
                $value = $accessor->getValue($holiday, $key);
                if ($value) {
                    $countBl++;
                    $inStates[] = $val;
                }
            }
        }

        /**
         * Check if we have the "short" flag
         * There can only be one result then, so skip the rest
         */
        if ($this->defaults['short'] === true) {
            $isHoliday = $holiday === null ? false : true;
            $name = $holiday === null ? null : $holiday->getHolidayName();
            $isGeneral = $this->defaults['bundesweit'];

            if ($holiday === null) {
                return array(
                    'isHoliday' => false,
                    'isGeneral' => false,
                    'name' => null,
                );
            }

            if ($this->defaults['bundeslaender'] === null) {
                return array(
                    'isHoliday' => $isHoliday,
                    'isGeneral' => $isGeneral,
                    'name' => $name,
                );
            } else {

                if (count($this->defaults['bundeslaender']) > 1) {
                    throw new ApiException('Short-Response can only contain 1 region');
                }

                reset($this->defaults['bundeslaender']);
                return array(
                    'isHoliday' => $accessor->getValue($holiday, key($this->defaults['bundeslaender'])),
                    'isGeneral' => $isGeneral,
                    'name' => $name,
                );
            }
        }

        /**
         * See if we only need the Info
         */
        if ($this->defaults['info'] === true) {

            if ($holiday === null) {
                return array(
                    'holiday' => null,
                );
            }

            $comments = array();
            foreach ($holiday->getComments() as $c) {
                $comments[] = $c->getComment();
            }

            return array(
                'holiday' => $day->format('Y-m-d'),
                'name' => $holiday->getHolidayName(),
                'comments' => $comments,
                'all_states' => $holiday->getBundesweit(),
                'in_regions' => $countBl,
                'regions' => $inStates,
            );
        }


        /**
         * Check which states are included
         */
        $regions = array();
        if ($holiday !== null) {
            if ($this->defaults['bundeslaender'] === null) {
                foreach ($this->bundeslaender as $key => $reg) {
                    $regions[$key] = $accessor->getValue($holiday, $key);
                }
            } else {

                foreach ($this->defaults['bundeslaender'] as $bl => $value) {
                    $regions[$bl] = $accessor->getValue($holiday, $bl);
                }

            }

            /**
             * Put it all together
             */
            $data = array(
                'date' => $day->format('Y-m-d'),
                'name' => $holiday->getHolidayName(),
                'regions' => $regions,
                'all_states' => $holiday->getBundesweit(),
            );

            if ($holiday->getBundesweit()) {
                $general = ' general';
            }

            if ($countBl > 0 && !$holiday->getBundesweit()) {
                $addBl = ' in ' . $countBl . ' regions';
            }

        }
        /**
         * Show if we have something
         */
        $result = $holiday === null ? false : true;
        $message = $holiday === null ? $d . ' is not a' . $general . ' holiday' : $d . ' is a' . $general . ' holiday' . $addBl;

        /**
         * Final Response
         */
        return array(
            'result' => $result,
            'message' => $message,
            'holiday' => $data
        );

    }

    /**
     * @param int $year
     * @return array
     * @throws ApiException
     */
    public function by_year(int $year)
    {

        $accessor = PropertyAccess::createPropertyAccessor();

        /**
         * Allow range from 1950 - 2050
         */
        if (!preg_match('/^(19[5-9]\d|20[0-4]\d|2050)$/', $year)) {
            throw new ApiException('Please select a date between 1950 and 2050');
        }

        $opt = array(
            'holidayYear' => array(
                null,
                $year
            )
        );

        /**
         * Parse additional params
         */
        try {
            self::parse_params();
        } catch (ApiException $e) {
            throw new ApiException($e);
        }

        if ($this->defaults['bundesweit'] === true) {
            $opt['bundesweit'] = true;
        }

        /**
         * Gather all holidays that have this year or null as 'holidayYear'
         */
        $holidays = $this->em->getRepository(Holiday::class)->findBy($opt);

        /**
         * Show if we have something
         */
        $result = empty($holidays) ? false : true;
        $message = empty($holidays) ? 'Could not find any holidays' : 'Holidays for ' . $year;

        /**
         * Fill our data array
         */
        $data = array();
        $regions = array();
        foreach ($holidays as $holiday) {

            /**
             * Check how many have this holiday
             */
            $countBl = 0;
            $inStates = array();
            foreach ($this->bundeslaender as $key => $val) {
                $value = $accessor->getValue($holiday, $key);
                if ($value) {
                    $countBl++;
                    $inStates[] = $val;
                }
            }

            /**
             * Construct the date
             */
            $date = \DateTime::createFromFormat('j.n.Y', $holiday->getHolidayDay() . '.' . $holiday->getHolidayMonth() . '.' . $year);
            if ($date === false) {
                throw new ApiException('Invalid Date: ' . $holiday->getHolidayDay() . '.' . $holiday->getHolidayMonth() . '.' . $year);
            }

            /**
             * Check which states are included
             */
            if ($this->defaults['bundeslaender'] === null) {
                foreach ($this->bundeslaender as $key => $reg) {
                    $regions[$key] = $accessor->getValue($holiday, $key);
                }
            } else {

                $regions = array();
                $accessor = PropertyAccess::createPropertyAccessor();

                foreach ($this->defaults['bundeslaender'] as $bl => $value) {
                    $regions[$bl] = $accessor->getValue($holiday, $bl);
                }

            }

            /**
             * See if we only need the Info
             */
            if ($this->defaults['info'] === true) {

                if ($holiday === null) {
                    return array(
                        'holiday' => null,
                    );
                }

                $comments = array();
                foreach ($holiday->getComments() as $c) {
                    $comments[] = $c->getComment();
                }

                $data[] = array(
                    'holiday' => $date->format('Y-m-d'),
                    'name' => $holiday->getHolidayName(),
                    'comments' => $comments,
                    'all_states' => $holiday->getBundesweit(),
                    'in_regions' => $countBl,
                    'regions' => $inStates,
                );
            } else {

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

    /**
     * @param bool $isDay
     * @throws ApiException
     */
    private function parse_params(bool $isDay = false)
    {

        /**
         * Check for unknown Params
         */
        foreach ($this->request->query as $key => $param) {

            if (!array_key_exists($key, $this->defaults)) {
                throw new ApiException('Unknown param: ' . $key);
            }
        }

        /**
         * Get allowed "bundesweit"
         */
        if ($this->request->query->get('bundesweit') === 'true' || $this->request->query->get('bundesweit') === '1') {
            $bundesweit = true;
        } elseif ($this->request->query->get('bundesweit') === 'false' || $this->request->query->get('bundesweit') === '0' || $this->request->query->get('bundesweit') === null) {
            $bundesweit = false;
        } else {
            throw new ApiException('Invalid value for param "bundesweit": ' . $this->request->query->get('bundesweit'));
        }

        $this->defaults['bundesweit'] = $bundesweit;

        /**
         * Get allowed "info"
         */
        if ($this->request->query->get('info') === 'true' || $this->request->query->get('info') === '1') {
            $info = true;
        } elseif ($this->request->query->get('info') === 'false' || $this->request->query->get('info') === '0' || $this->request->query->get('info') === null) {
            $info = false;
        } else {
            throw new ApiException('Invalid value for param "info": ' . $this->request->query->get('info'));
        }

        /**
         * Get allowed "short"
         */
        if ($this->request->query->get('short') === 'true' || $this->request->query->get('short') === '1') {
            $short = true;
        } elseif ($this->request->query->get('short') === 'false' || $this->request->query->get('short') === '0' || $this->request->query->get('short') === null) {
            $short = false;
        } else {
            throw new ApiException('Invalid value for param "short": ' . $this->request->query->get('short'));
        }

        /**
         * Set the Bundeslaender
         */
        $hasBL = false;
        $bundeslaender = $this->request->query->get('bundeslaender');
        if ($bundeslaender !== null) {

            if ($bundesweit && $this->request->query->get('bundesweit') !== null) {
                throw new ApiException('Param mismatch. Cannot use "bundeslaender" and "bundesweit" together');
            }

            /**
             * Explode the Bundeslaender
             */
            $bs = explode(',', $bundeslaender);

            if (count($bs) > 1) {
                $hasBL = true;
            }

            /**
             * Check if we know the Bundesland
             */
            foreach ($bs as $b) {
                if (!array_key_exists($b, $this->bundeslaender)) {
                    throw new ApiException('Unknown Bundesland: ' . $b);
                }

                $this->defaults['bundeslaender'][$b] = true;
            }
        }

        /**
         * Set the Short
         */
        if ($short) {

            if ($hasBL) {
                throw new ApiException('No Short-Response available if >1 Bundesland selected selected');
            }

            if (!$isDay) {
                throw new ApiException('No Short-Response available if not querying for specific date');
            }

            $this->defaults['short'] = $short;

        }

        /**
         * Set the Info
         */
        if ($info) {
            $this->defaults['info'] = $info;
        }

    }

}