<?php

namespace App\Repository;

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
     * @return Post[]
     */
    public function findAllPostsOrderedByNewest()
    {
        return $this->createQueryBuilder('q')
            ->orderBy('q.created_at', 'Desc')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param string $id
     * @return Post|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findOnePostById(string $id): ?Post
    {

        $formatId = intval($id);

        return $formatId === 0 ?
            null :
            $this->createQueryBuilder('q')
            ->andWhere('q.id = :id')
            ->setParameter('id', $formatId)
            ->getQuery()
            ->getOneOrNullResult();
    }


    /**
     * @param string $title
     * @return Post[] Returns an array of Post objects
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findManyByCategory(string $category)
    {
        return $this->createQueryBuilder('p')
            ->where('p.category = :val')
            ->setParameter('val', $category)
            ->getQuery()
            ->getResult()
        ;
    }

    // /**
    //  * @return Post[] Returns an array of Post objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

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
