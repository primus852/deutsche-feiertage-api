<?php

namespace App\Tests\Helper;

use App\Entity\Holiday;
use App\Entity\HolidayApplicability;
use App\Entity\HolidayRule;
use App\Entity\RuleType;
use App\Entity\State;
use App\Enum\HolidayRuleType;
use App\Repository\HolidayRuleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Uid\Uuid;

readonly class EntityMockHelper
{
    public function __construct(private \Closure $mockFactory)
    {
    }

    /**
     * Create mock objects for all 16 German states
     * @return State[]
     */
    public function createMockStates(): array
    {
        $stateData = [
            ['BW', 'Baden-Württemberg'],
            ['BY', 'Bayern'],
            ['BE', 'Berlin'],
            ['BB', 'Brandenburg'],
            ['HB', 'Bremen'],
            ['HH', 'Hamburg'],
            ['HE', 'Hessen'],
            ['MV', 'Mecklenburg-Vorpommern'],
            ['NI', 'Niedersachsen'],
            ['NW', 'Nordrhein-Westfalen'],
            ['RP', 'Rheinland-Pfalz'],
            ['SL', 'Saarland'],
            ['SN', 'Sachsen'],
            ['ST', 'Sachsen-Anhalt'],
            ['SH', 'Schleswig-Holstein'],
            ['TH', 'Thüringen'],
        ];

        $states = [];
        foreach ($stateData as [$symbol, $name]) {
            $states[] = $this->createMockState($symbol, $name);
        }

        return $states;
    }

    /**
     * Create a mock State entity
     */
    public function createMockState(string $symbol, string $name): State|MockObject
    {
        $state = ($this->mockFactory)(State::class);
        $state->method('getSymbol')->willReturn($symbol);
        $state->method('getName')->willReturn($name);
        return $state;
    }

    /**
     * Create a mock Holiday entity
     */
    public function createMockHoliday(string $name, ?string $description = null): Holiday|MockObject
    {
        $holiday = ($this->mockFactory)(Holiday::class);
        $holiday->method('getId')->willReturn(Uuid::v4());
        $holiday->method('getName')->willReturn($name);
        $holiday->method('getDescription')->willReturn($description);

        $applicabilities = new ArrayCollection();
        $holiday->method('getHolidayApplicabilities')->willReturn($applicabilities);

        return $holiday;
    }

    /**
     * Create a mock RuleType entity
     */
    public function createMockRuleType(HolidayRuleType $type): RuleType|MockObject
    {
        $ruleType = ($this->mockFactory)(RuleType::class);
        $ruleType->method('getName')->willReturn($type);
        return $ruleType;
    }

    /**
     * Create a mock HolidayRule entity
     */
    public function createMockHolidayRule(
        Holiday $holiday,
        RuleType $ruleType,
        ?int $month = null,
        ?int $day = null,
        ?int $year = null,
        ?int $offsetDays = null
    ): HolidayRule|MockObject {
        $rule = ($this->mockFactory)(HolidayRule::class);
        $rule->method('getHoliday')->willReturn($holiday);
        $rule->method('getRuleType')->willReturn($ruleType);
        $rule->method('getMonth')->willReturn($month);
        $rule->method('getDay')->willReturn($day);
        $rule->method('getYear')->willReturn($year);
        $rule->method('getOffsetDays')->willReturn($offsetDays);
        return $rule;
    }

    /**
     * Create a mock HolidayApplicability entity
     */
    public function createMockHolidayApplicability(Holiday $holiday, State $state): HolidayApplicability|MockObject
    {
        $applicability = ($this->mockFactory)(HolidayApplicability::class);
        $applicability->method('getHoliday')->willReturn($holiday);
        $applicability->method('getState')->willReturn($state);
        return $applicability;
    }

    /**
     * Setup HolidayRuleRepository mock to return the given rules
     * @param HolidayRuleRepository|MockObject $repository
     * @param HolidayRule[] $rules
     */
    public function setupHolidayRuleRepositoryMock(MockObject $repository, array $rules): void
    {
        $queryBuilder = ($this->mockFactory)(QueryBuilder::class);
        $query = ($this->mockFactory)(Query::class);

        $repository->method('createQueryBuilder')->willReturn($queryBuilder);
        $queryBuilder->method('select')->willReturnSelf();
        $queryBuilder->method('leftJoin')->willReturnSelf();
        $queryBuilder->method('getQuery')->willReturn($query);
        $query->method('getResult')->willReturn($rules);
    }
}
