<?php

namespace AppBundle\Repository;

/**
 * PostRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PostRepository extends \Doctrine\ORM\EntityRepository
{

    public function findWithRegion(int $id){
        return $this->createQueryBuilder('p')
            ->select('p, c, d, r')
            ->join('p.city', 'c')
            ->join('c.department', 'd')
            ->join('d.region', 'r')
            ->where('p.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getSingleResult();
    }

    public function findAllWithRegion(){
        return $this->createQueryBuilder('p')
            ->select('p, c, d, r')
            ->join('p.city', 'c')
            ->join('c.department', 'd')
            ->join('d.region', 'r')
            ->getQuery()
            ->getResult();
    }
}
