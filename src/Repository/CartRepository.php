<?php

namespace App\Repository;

use App\Entity\Cart;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Cart>
 *
 * @method Cart|null find($id, $lockMode = null, $lockVersion = null)
 * @method Cart|null findOneBy(array $criteria, array $orderBy = null)
 * @method Cart[]    findAll()
 * @method Cart[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CartRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cart::class);
    }

    public function save(Cart $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Cart $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

   /**
    * @return Cart[] Returns an array of Cart objects
    */
   public function findActiveCart($user_id): ?Cart
   {
       return $this->createQueryBuilder('c')
           ->andWhere('c.user = :id')
           ->andWhere('c.status = 0')
           ->setParameter('id', $user_id)
           ->getQuery()
           ->getOneOrNullResult()
       ;
   }


//    public function findActiveCart($user_id): ?Cart
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.user = :id')
//            ->andWhere('c.status = 0')
//            ->setParameter('id', $user_id)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
