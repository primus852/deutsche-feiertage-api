<?php

namespace App\Command;

use App\Entity\Holiday;
use App\Repository\HolidayRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:add-holiday',
    description: 'Add a holiday (source: https://de.wikipedia.org/wiki/Gesetzliche_Feiertage_in_Deutschland)',
)]
class AddHolidayCommand extends Command
{

    public function __construct(
        private readonly HolidayRepository $holidayRepository,
        string  $name = null
    )
    {
        parent::__construct($name);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $helper = $this->getHelper('question');


        while (true) {

            $holiday = new Holiday();

            /**
             * Dates
             */
            $qHolidayDate = new Question("Please enter the date\n");
            $aHolidayDate = $helper->ask($input, $output, $qHolidayDate);
            $dates = $this->_parseDate($aHolidayDate);

            $holiday->setHolidayDay($dates['day']);
            $holiday->setHolidayMonth($dates['month']);
            $holiday->setHolidayYear($dates['year']);

            /**
             * Name
             */
            $qHolidayName = new Question("Please enter the name\n");
            $aHolidayName = $helper->ask($input, $output, $qHolidayName);
            $holiday->setHolidayName($aHolidayName);

            /**
             * Bundeseinheitlich?
             */
            $qBundesweit = new ConfirmationQuestion("Bundesweit? (y/n, default = n)\n", false, '/^(y|j)/i');
            $aBundesweit = $helper->ask($input, $output, $qBundesweit);
            $holiday->setIsBundesweit($aBundesweit);

            /**
             * Bundesländerspezifisch
             */
            $laender = array(
                'Baden-Würtemberg' => 'Bw',
                'Bayern' => 'Bay',
                'Berlin' => 'Be',
                'Brandenburg' => 'Bb',
                'Bremen' => 'Hb',
                'Hamburg' => 'Hh',
                'Hessen' => 'He',
                'Mecklenburg-Vorpommern' => 'Mv',
                'Niedersachsen' => 'Ni',
                'Nordrhein-Westphalen' => 'Nw',
                'Rheinland-Pfalz' => 'Rp',
                'Saarland' => 'Sl',
                'Sachsen' => 'Sn',
                'Sachsen-Anhalt' => 'St',
                'Schleswig-Holstein' => 'Sh',
                'Thüringen' => 'Th'
            );

            foreach ($laender as $name => $land) {

                if (!$aBundesweit) {
                    $qLand = new ConfirmationQuestion("in " . $name . " (".$land.")? (y/n, default = n)\n", false, '/^(y|j)/i');
                    $aLand = $helper->ask($input, $output, $qLand);
                    $holiday = $this->_updateHolidayBundesland($land, $aLand, $holiday);
                } else {
                    $holiday = $this->_updateHolidayBundesland($land, true, $holiday);
                }
            }

            $this->holidayRepository->addOrUpdate($holiday, $laender);
            $io->success('Holiday added!');

            $qAgain = new ConfirmationQuestion("another? (y/n, default = y)\n", true, '/^(y|j)/i');
            $aAgain = $helper->ask($input, $output, $qAgain);

            if (!$aAgain) {
                break;
            }
        }

        return Command::SUCCESS;
    }

    /**
     * @param string $landShort
     * @param string $value
     * @param Holiday $holiday
     * @return Holiday
     */
    private function _updateHolidayBundesland(string $landShort, string $value, Holiday $holiday): Holiday
    {
        $holiday->{"setIs" . $landShort}($value);

        return $holiday;
    }

    /**
     * @param string $dateInput
     * @return array|bool
     */
    private function _parseDate(string $dateInput): array|bool
    {
        if (strlen($dateInput) === 4) {
            return array(
                'day' => (int)substr($dateInput, 0, 2),
                'month' => (int)substr($dateInput, 2, 2),
                'year' => null
            );
        }

        if (strlen($dateInput) === 6) {
            return array(
                'day' => (int)substr($dateInput, 0, 2),
                'month' => (int)substr($dateInput, 2, 2),
                'year' => (int)("20" . substr($dateInput, 4, 2)),
            );
        }

        if (strlen($dateInput) === 8) {
            return array(
                'day' => (int)substr($dateInput, 0, 2),
                'month' => (int)substr($dateInput, 2, 2),
                'year' => (int)substr($dateInput, 4, 4),
            );
        }

        return false;
    }
}
