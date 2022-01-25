<?php

namespace App\Repository;

use App\Entity\Comment;
use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

    /**
     * @param array $search
     * @return int|mixed|string
     */
    public function search(array $search)
    {
        $params = [];
        $qb     = $this->createQueryBuilder('p');
        $qb->orderBy('p.name', 'ASC');

        if (isset($search['nom']) && $search['nom'] !== '') {
            $qb->andWhere('p.name LIKE :name');
            $params['name'] = $search['nom'] . '%';
        }

        if (isset($search['hasComment'])) {
            $qb->join('p.comments', 'c');
        }

        if (isset($search['auteur']) && $search['auteur'] !== '') {
            $qb->join('p.user', 'u');
            $qb->andWhere('u.email LIKE :auteur');
            $params['auteur'] = $search['auteur'] . '%';
        }

        if (isset($search['date']) && $search['date'] !== '') {
            $qb->andWhere('p.updatedAt >= :date');
            $params['date'] = $search['date'];
        }

//        if ($this->security->isGranted('ROLE_USER')) {
//            $qb->andWhere('p.updatedAt >= :date');
//            $params['date'] = $search['date'];
//        }
        $qb->setParameters($params);

        return $qb->getQuery()->getResult();
    }
//     /**
//      * @return Post[] Returns an array of Post objects
//      */
//    public function findByExampleField($value)
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }
//

    /*
    public function findOneBySomeField($value): ?Post
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
