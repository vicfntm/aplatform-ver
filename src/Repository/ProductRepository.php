<?php

namespace App\Repository;

use App\DTO\OrderCommodityDto;
use App\DTO\ProductDto;
use App\Entity\Commodity;
use App\Entity\Product;
use App\Service\CommodityCounter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ReadableCollection;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 *
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{


    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function findCommoditySet(string $productId): OrderCommodityDto
    {
        $repo = $this->createQueryBuilder('q')->getEntityManager()->getRepository(Product::class);
        $product = $repo->findOneBy(['id' => $productId]);
        $root = new CommodityCounter();

        return $root->aggregate(commodities: $product->getCommodities());
    }


    /**
     * @return Product[] Returns an array of Product objects
     */
    public function findProductSet(array $productIds): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.id IN (:val)')
            ->setParameter('val', $productIds)
            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
            ;
    }
//    /**
//     * @return Product[] Returns an array of Product objects
//     */
//    public function findByExampleField($value): array
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

//    public function findOneBySomeField($value): ?Product
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
