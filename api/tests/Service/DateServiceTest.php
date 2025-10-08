<?php

namespace App\Tests\Service;

use App\Service\DateService;
use App\Repository\HolidayRuleRepository;
use App\Repository\StateRepository;
use App\Enum\HolidayRuleType;
use App\Exception\ApiException;
use App\Tests\Helper\EntityMockHelper;
use PHPUnit\Framework\TestCase;

class DateServiceTest extends TestCase
{
    private DateService $dateService;
    private HolidayRuleRepository $holidayRuleRepository;
    private StateRepository $stateRepository;
    private EntityMockHelper $mockHelper;

    protected function setUp(): void
    {
        $this->holidayRuleRepository = $this->createMock(HolidayRuleRepository::class);
        $this->stateRepository = $this->createMock(StateRepository::class);

        $this->mockHelper = new EntityMockHelper(fn(string $class) => $this->createMock($class));

        $this->dateService = new DateService(
            $this->holidayRuleRepository,
            $this->stateRepository
        );
    }

    public function testGetHolidayForDateWithNationalHoliday(): void
    {
        // Arrange
        $dateString = '2025-01-01';

        // Mock all 16 German states
        $states = $this->mockHelper->createMockStates();
        $this->stateRepository->method('findAll')->willReturn($states);

        // Mock Neujahr (New Year) - national holiday
        $holiday = $this->mockHelper->createMockHoliday('Neujahr', 'Erster Tag des Jahres');
        $ruleType = $this->mockHelper->createMockRuleType(HolidayRuleType::FIXED);
        $holidayRule = $this->mockHelper->createMockHolidayRule($holiday, $ruleType, 1, 1);

        // Add applicabilities for all states
        foreach ($states as $state) {
            $applicability = $this->mockHelper->createMockHolidayApplicability($holiday, $state);
            $holiday->getHolidayApplicabilities()->add($applicability);
        }

        $this->mockHelper->setupHolidayRuleRepositoryMock($this->holidayRuleRepository, [$holidayRule]);

        // Act
        $response = $this->dateService->getHolidayForDate($dateString);

        // Assert
        $this->assertEquals(200, $response->getStatusCode());
        $data = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $this->assertEquals('2025-01-01', $data['data']['date']);
        $this->assertTrue($data['data']['isHoliday']);
        $this->assertTrue($data['data']['isNational']);
        $this->assertNotNull($data['data']['holiday']);
        $this->assertEquals('Neujahr', $data['data']['holiday']['name']);
        $this->assertCount(16, $data['data']['applicable']);
    }

    public function testGetHolidayForDateWithRegionalHoliday(): void
    {
        // Arrange
        $dateString = '2025-01-06';

        // Mock all 16 German states
        $states = $this->mockHelper->createMockStates();
        $this->stateRepository->method('findAll')->willReturn($states);

        // Mock Heilige Drei Könige - only BW, BY, ST
        $holiday = $this->mockHelper->createMockHoliday('Heilige Drei Könige', 'Epiphanias');
        $ruleType = $this->mockHelper->createMockRuleType(HolidayRuleType::FIXED);
        $holidayRule = $this->mockHelper->createMockHolidayRule($holiday, $ruleType, 1, 6);

        // Add applicabilities only for BW, BY, ST
        $applicableStateSymbols = ['BW', 'BY', 'ST'];
        foreach ($states as $state) {
            if (in_array($state->getSymbol(), $applicableStateSymbols, true)) {
                $applicability = $this->mockHelper->createMockHolidayApplicability($holiday, $state);
                $holiday->getHolidayApplicabilities()->add($applicability);
            }
        }

        $this->mockHelper->setupHolidayRuleRepositoryMock($this->holidayRuleRepository, [$holidayRule]);

        // Act
        $response = $this->dateService->getHolidayForDate($dateString);

        // Assert
        $data = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $this->assertEquals('2025-01-06', $data['data']['date']);
        $this->assertTrue($data['data']['isHoliday']);
        $this->assertFalse($data['data']['isNational']);
        $this->assertEquals('Heilige Drei Könige', $data['data']['holiday']['name']);
        $this->assertCount(3, $data['data']['applicable']);

        // Check that only BW, BY, ST are in applicable
        $applicableSymbols = array_map(static fn($state) => $state['symbol'], $data['data']['applicable']);
        $this->assertContains('BW', $applicableSymbols);
        $this->assertContains('BY', $applicableSymbols);
        $this->assertContains('ST', $applicableSymbols);
    }

    public function testGetHolidayForDateWithNoHoliday(): void
    {
        // Arrange
        $dateString = '2025-01-02';

        $states = $this->mockHelper->createMockStates();
        $this->stateRepository->method('findAll')->willReturn($states);

        // Mock a holiday that doesn't match the date
        $holiday = $this->mockHelper->createMockHoliday('Neujahr', 'Erster Tag des Jahres');
        $ruleType = $this->mockHelper->createMockRuleType(HolidayRuleType::FIXED);
        $holidayRule = $this->mockHelper->createMockHolidayRule($holiday, $ruleType, 1, 1);

        $this->mockHelper->setupHolidayRuleRepositoryMock($this->holidayRuleRepository, [$holidayRule]);

        // Act
        $response = $this->dateService->getHolidayForDate($dateString);

        // Assert
        $data = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $this->assertEquals('2025-01-02', $data['data']['date']);
        $this->assertFalse($data['data']['isHoliday']);
        $this->assertFalse($data['data']['isNational']);
        $this->assertNull($data['data']['holiday']);
        $this->assertEmpty($data['data']['applicable']);
    }

    public function testGetHolidayForDateWithStateFilter(): void
    {
        // Arrange
        $dateString = '2025-01-06';
        $stateSymbol = 'BY';

        $states = $this->mockHelper->createMockStates();
        $this->stateRepository->method('findAll')->willReturn($states);

        // Mock Heilige Drei Könige - only BW, BY, ST
        $holiday = $this->mockHelper->createMockHoliday('Heilige Drei Könige', 'Epiphanias');
        $ruleType = $this->mockHelper->createMockRuleType(HolidayRuleType::FIXED);
        $holidayRule = $this->mockHelper->createMockHolidayRule($holiday, $ruleType, 1, 6);

        $applicableStateSymbols = ['BW', 'BY', 'ST'];
        foreach ($states as $state) {
            if (in_array($state->getSymbol(), $applicableStateSymbols, true)) {
                $applicability = $this->mockHelper->createMockHolidayApplicability($holiday, $state);
                $holiday->getHolidayApplicabilities()->add($applicability);
            }
        }

        $this->mockHelper->setupHolidayRuleRepositoryMock($this->holidayRuleRepository, [$holidayRule]);

        // Act
        $response = $this->dateService->getHolidayForDate($dateString, $stateSymbol);

        // Assert
        $data = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $this->assertTrue($data['data']['isHoliday']);
        $this->assertCount(1, $data['data']['applicable']);
        $this->assertEquals('BY', $data['data']['applicable'][0]['symbol']);
    }

    public function testGetHolidayForDateWithStateFilterNotApplicable(): void
    {
        // Arrange
        $dateString = '2025-01-06';
        $stateSymbol = 'NW'; // Not applicable for Heilige Drei Könige

        $states = $this->mockHelper->createMockStates();
        $this->stateRepository->method('findAll')->willReturn($states);

        // Mock Heilige Drei Könige - only BW, BY, ST
        $holiday = $this->mockHelper->createMockHoliday('Heilige Drei Könige', 'Epiphanias');
        $ruleType = $this->mockHelper->createMockRuleType(HolidayRuleType::FIXED);
        $holidayRule = $this->mockHelper->createMockHolidayRule($holiday, $ruleType, 1, 6);

        $applicableStateSymbols = ['BW', 'BY', 'ST'];
        foreach ($states as $state) {
            if (in_array($state->getSymbol(), $applicableStateSymbols, true)) {
                $applicability = $this->mockHelper->createMockHolidayApplicability($holiday, $state);
                $holiday->getHolidayApplicabilities()->add($applicability);
            }
        }

        $this->mockHelper->setupHolidayRuleRepositoryMock($this->holidayRuleRepository, [$holidayRule]);

        // Act
        $response = $this->dateService->getHolidayForDate($dateString, $stateSymbol);

        // Assert
        $data = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $this->assertFalse($data['data']['isHoliday']);
        $this->assertEmpty($data['data']['applicable']);
    }

    public function testGetHolidayForDateWithEasterRelatedHoliday(): void
    {
        // Arrange
        $dateString = '2025-04-18'; // Good Friday 2025

        $states = $this->mockHelper->createMockStates();
        $this->stateRepository->method('findAll')->willReturn($states);

        // Mock Karfreitag (Good Friday) - 2 days before Easter
        $holiday = $this->mockHelper->createMockHoliday('Karfreitag', 'Good Friday');
        $ruleType = $this->mockHelper->createMockRuleType(HolidayRuleType::RELATIVE_TO_EASTER);
        $holidayRule = $this->mockHelper->createMockHolidayRule($holiday, $ruleType, null, null, null, -2);

        foreach ($states as $state) {
            $applicability = $this->mockHelper->createMockHolidayApplicability($holiday, $state);
            $holiday->getHolidayApplicabilities()->add($applicability);
        }

        $this->mockHelper->setupHolidayRuleRepositoryMock($this->holidayRuleRepository, [$holidayRule]);

        // Act
        $response = $this->dateService->getHolidayForDate($dateString);

        // Assert
        $data = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $this->assertTrue($data['data']['isHoliday']);
        $this->assertEquals('Karfreitag', $data['data']['holiday']['name']);
    }

    public function testGetHolidayForDateWithInvalidDateFormat(): void
    {
        // Expect exception
        $this->expectException(ApiException::class);

        // Act
        $this->dateService->getHolidayForDate('invalid-date');
    }
}
