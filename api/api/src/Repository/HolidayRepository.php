<?php

namespace App\Repository;

use App\Entity\Holiday;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\Persistence\ManagerRegistry;

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
     * @param Holiday $entity
     * @param bool $flush
     * @return void
     */
    public function add(Holiday $entity, bool $flush = false): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @param Holiday $entity
     * @param bool $flush
     * @return void
     */
    public function remove(Holiday $entity, bool $flush = false): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @param Holiday $entity
     * @param array $laender
     * @return Holiday
     */
    public function addOrUpdate(Holiday $entity, array $laender): Holiday
    {
        $exists = $this->findOneBy(array(
            'holidayDay' => $entity->getHolidayDay(),
            'holidayMonth' => $entity->getHolidayMonth(),
            'holidayYear' => $entity->getHolidayYear(),
        ));

        if($exists){
            $holiday = $exists;
        }else{
            $holiday = new Holiday();
        }

        $holiday->setHolidayDay($entity->getHolidayDay());
        $holiday->setHolidayMonth($entity->getHolidayMonth());
        $holiday->setHolidayYear($entity->getHolidayYear());
        $holiday->setIsBundesweit($entity->getIsBundesweit());
        foreach ($laender as $land){
            $holiday->{"setIs".$land}($entity->{"getIs".$land}());
        }

        $this->_em->persist($entity);
        $this->_em->flush();

        return $entity;
    }

}
