<?php
/**
 * Created by PhpStorm.
 * User: nikola
 * Date: 20.7.17.
 * Time: 23.00
 */

namespace AppBundle\Repository;


class NoteRepository extends \Doctrine\ORM\EntityRepository
{
    public function getTrashed()
    {
        $qb = $this->createQueryBuilder('e')
            ->where('e.trashed = true');

        return $qb->getQuery()->execute();
    }
}