<?php

namespace App\Service;

use App\DTO\DateHolidayResponseDTO;
use App\DTO\HolidayDTO;
use App\DTO\HolidayStateDTO;
use App\Entity\Holiday;
use App\Entity\HolidayRule;
use App\Enum\HolidayRuleType;
use App\Exception\ApiException;
use App\Mapping\ApiResponse;
use App\Repository\HolidayRuleRepository;
use App\Repository\StateRepository;
use DateTime;
use Symfony\Component\HttpFoundation\JsonResponse;

readonly class DateService
{
    public function __construct(
        private HolidayRuleRepository $holidayRuleRepository,
        private StateRepository $stateRepository,
    )
    {
    }

    /**
     * Get holidays for a specific date
     *
     * @param string $dateString The date to check for holidays Y-m-d format
     * @param string|null $stateSymbol Optional state symbol to filter by (e.g., 'BY', 'NW')
     * @return JsonResponse Array of holidays for the given date
     * @throws ApiException
     */
    public function getHolidayForDate(string $dateString, ?string $stateSymbol = null): JsonResponse
    {

        $date = DateTime::createFromFormat('Y-m-d', $dateString);
        if (!$date) {
            throw ApiException::badRequest(sprintf('Invalid date format: %s', $dateString), 'INVALID_DATE_FORMAT');
        }

        $year = (int)$date->format('Y');
        $month = (int)$date->format('n');
        $day = (int)$date->format('j');

        // Get all states
        $allStates = $this->stateRepository->findAll();

        // Get all holiday rules with their relationships
        $queryBuilder = $this->holidayRuleRepository->createQueryBuilder('hr')
            ->select('hr', 'h', 'rt', 'ha', 's')
            ->leftJoin('hr.holiday', 'h')
            ->leftJoin('hr.ruleType', 'rt')
            ->leftJoin('h.holidayApplicabilities', 'ha')
            ->leftJoin('ha.state', 's');

        $holidayRules = $queryBuilder->getQuery()->getResult();

        // Group rules by holiday
        $holidayRulesGrouped = [];
        foreach ($holidayRules as $rule) {
            $holiday = $rule->getHoliday();
            if ($holiday) {
                $holidayId = $holiday->getId()->toString();
                if (!isset($holidayRulesGrouped[$holidayId])) {
                    $holidayRulesGrouped[$holidayId] = [
                        'holiday' => $holiday,
                        'rules' => [],
                    ];
                }
                $holidayRulesGrouped[$holidayId]['rules'][] = $rule;
            }
        }

        // Check which holidays apply to the given date
        $holidaysForDate = [];
        $allApplicableStates = [];

        foreach ($holidayRulesGrouped as $holidayData) {
            /** @var Holiday $holiday */
            $holiday = $holidayData['holiday'];
            $rules = $holidayData['rules'];

            // Check if any rule matches the date
            $matchesDate = false;
            foreach ($rules as $rule) {
                try {
                    if ($this->isHolidayOnDate($rule, $date, $year, $month, $day)) {
                        $matchesDate = true;
                        break;
                    }
                } catch (\Exception $exception) {
                    throw ApiException::badRequest(
                        sprintf('Error checking holiday rule: %s', $exception->getMessage()),
                        'HOLIDAY_RULE_ERROR'
                    );
                }
            }

            if ($matchesDate) {
                // Add holiday to the list
                $holidaysForDate[] = new HolidayDTO(
                    name: $holiday->getName(),
                    description: $holiday->getDescription()
                );

                // Collect applicable states for this holiday
                foreach ($holiday->getHolidayApplicabilities() as $applicability) {
                    $state = $applicability->getState();
                    if ($state) {
                        $stateSymbolKey = $state->getSymbol();
                        if (!isset($allApplicableStates[$stateSymbolKey])) {
                            $allApplicableStates[$stateSymbolKey] = $state;
                        }
                    }
                }
            }
        }

        // Determine if this is a holiday and if it's national
        $isHoliday = !empty($holidaysForDate);
        $isNational = $isHoliday && count($allApplicableStates) === count($allStates);

        // Build applicable states DTOs
        $applicableStateDTOs = [];
        foreach ($allApplicableStates as $state) {
            // Apply state filter if provided
            if ($stateSymbol !== null && $state->getSymbol() !== $stateSymbol) {
                continue;
            }

            $applicableStateDTOs[] = new HolidayStateDTO(
                symbol: $state->getSymbol(),
                name: $state->getName()
            );
        }

        // If state filter is provided and this state is not in the applicable list, set isHoliday to false
        if ($stateSymbol !== null && $isHoliday) {
            $filteredStateIsHoliday = false;
            foreach ($allApplicableStates as $state) {
                if ($state->getSymbol() === $stateSymbol) {
                    $filteredStateIsHoliday = true;
                    break;
                }
            }
            $isHoliday = $filteredStateIsHoliday;
        }

        // Always return a response
        $response = new DateHolidayResponseDTO(
            date: $dateString,
            isHoliday: $isHoliday,
            isNational: $isNational,
            holidays: $holidaysForDate,
            applicable: $applicableStateDTOs
        );

        return ApiResponse::success('OK', 200, $response->toArray());
    }

    /**
     * Check if a holiday rule matches the given date
     * @throws \DateMalformedStringException
     */
    private function isHolidayOnDate(HolidayRule $rule, DateTime $date, int $year, int $month, int $day): bool
    {

        $ruleTypeName = $rule->getRuleType()?->getName();

        return match ($ruleTypeName) {
            HolidayRuleType::FIXED => $this->isFixedHolidayOnDate($rule, $month, $day),
            HolidayRuleType::EASTER => $this->isEasterHolidayOnDate($rule, $year, $month, $day),
            HolidayRuleType::RELATIVE_TO_EASTER => $this->isRelativeEasterHolidayOnDate($rule, $date),
            HolidayRuleType::ONE_OFF => $this->isOneOffHolidayOnDate($rule, $year, $month, $day),
            default => false,
        };
    }

    /**
     * Check if fixed holiday matches the date
     */
    private function isFixedHolidayOnDate(HolidayRule $rule, int $month, int $day): bool
    {
        return $rule->getMonth() === $month && $rule->getDay() === $day;
    }

    /**
     * Check if Easter holiday matches the date
     */
    private function isEasterHolidayOnDate(HolidayRule $rule, int $year, int $month, int $day): bool
    {
        return $rule->getYear() === $year &&
            $rule->getMonth() === $month &&
            $rule->getDay() === $day;
    }

    /**
     * Check if relative Easter holiday matches the date
     * @throws \DateMalformedStringException
     */
    private function isRelativeEasterHolidayOnDate(HolidayRule $rule, DateTime $date): bool
    {
        $year = (int)$date->format('Y');
        $easterDate = $this->calculateEasterDate($year);

        if ($rule->getOffsetDays() !== null) {
            $holidayDate = clone $easterDate;
            $holidayDate->modify($rule->getOffsetDays() . ' days');

            return $holidayDate->format('Y-m-d') === $date->format('Y-m-d');
        }

        return false;
    }

    /**
     * Check if one-off holiday matches the date
     */
    private function isOneOffHolidayOnDate(HolidayRule $rule, int $year, int $month, int $day): bool
    {
        return $rule->getYear() === $year &&
            $rule->getMonth() === $month &&
            $rule->getDay() === $day;
    }

    /**
     * Calculate Easter date for a given year using the algorithm
     */
    private function calculateEasterDate(int $year): DateTime
    {
        // Using the algorithm for calculating Easter date
        $a = $year % 19;
        $b = (int)($year / 100);
        $c = $year % 100;
        $d = (int)($b / 4);
        $e = $b % 4;
        $f = (int)(($b + 8) / 25);
        $g = (int)(($b - $f + 1) / 3);
        $h = (19 * $a + $b - $d - $g + 15) % 30;
        $i = (int)($c / 4);
        $k = $c % 4;
        $l = (32 + 2 * $e + 2 * $i - $h - $k) % 7;
        $m = (int)(($a + 11 * $h + 22 * $l) / 451);
        $month = (int)(($h + $l - 7 * $m + 114) / 31);
        $day = (($h + $l - 7 * $m + 114) % 31) + 1;

        return new DateTime("$year-$month-$day");
    }
}
