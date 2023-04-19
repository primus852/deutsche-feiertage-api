<?php

namespace App\Repository;

use App\Entity\Holiday;
use App\Exception\DFAException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

/**
 * @extends ServiceEntityRepository<Holiday>
 *
 * @method Holiday|null find($id, $lockMode = null, $lockVersion = null)
 * @method Holiday|null findOneBy(array $criteria, array $orderBy = null)
 * @method Holiday[]    findAll()
 * @method Holiday[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HolidayRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Holiday::class);
    }

    /**
     * @param string $dateString
     * @return Holiday|null
     * @throws DFAException
     */
    public function findByDateString(string $dateString): Holiday|null
    {
        // Parse the Date
        $date = \DateTime::createFromFormat('Y-m-d', $dateString);

        if($date === false){
            throw new DFAException('INVALID_DATE');
        }

        $queryNoYear = $this->createQueryBuilder('h')
            ->where('h.holidayDay = :day')
            ->andWhere('h.holidayMonth = :month')
            ->setParameter('day', (int)$date->format('j'))
            ->setParameter('month', (int)$date->format('n'));

        $holidays = $queryNoYear
            ->getQuery()
            ->getResult();

        if(count($holidays) === 0){
            return null;
        }

        if(count($holidays) === 1){
            return $holidays[0];
        }

        foreach($holidays as $holiday){
            if($holiday->getHolidayYear() === (int)$date->format('Y')){
                return $holiday;
            }
        }

        return null;
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findByDateParams(int $day, int $month, string $compareId = null): Holiday|null
    {
        $query = $this->createQueryBuilder('h')
            ->where('h.holidayDay = :day')
            ->andWhere('h.holidayMonth = :month')
            ->setParameter('day', $day)
            ->setParameter('month', $month);

        $holiday = $query->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        if ($holiday !== null) {
            if ($compareId !== null) {
                if ($compareId === (string)$holiday->getId()) {
                    return null;
                }
            }
        }

        return $holiday;
    }

    public function save(Holiday $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Holiday $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
