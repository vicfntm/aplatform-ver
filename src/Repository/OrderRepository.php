<?php

namespace App\Repository;

use App\DTO\CommodityContainer;
use App\DTO\CommodityDto;
use App\DTO\GroupDto;
use App\Entity\Order;
use App\Service\CommodityCounter;
use App\Service\GroupData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Ulid;
use Doctrine\DBAL\Exception as DbalException;


/**
 * @extends ServiceEntityRepository<Order>
 *
 * @method Order|null find($id, $lockMode = null, $lockVersion = null)
 * @method Order|null findOneBy(array $criteria, array $orderBy = null)
 * @method Order[]    findAll()
 * @method Order[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderRepository extends ServiceEntityRepository implements LastCommoditySet
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    public function defineCommoditySet(string $orderId)
    {
        $id = Ulid::fromString($orderId);
        $repo = $this->createQueryBuilder('q')->getEntityManager()->getRepository(Order::class);
        $order = $repo->findOneBy(['id' => $id->toRfc4122()]);

        return $order->getCommodities();
    }

    public function findOperations(string $orderId): CommodityContainer
    {
        $id = Ulid::fromString($orderId);
        $this->findLastCommodities($id->toRfc4122());

        $tst = $this->createQueryBuilder('q')->getEntityManager()->createQuery(
            "select c, o, p from App\Entity\Commodity c JOIN c.relatedOrder o JOIN c.product p where c.relatedOrder = ?1"
        );
        $tst->setParameter(1, $id->toRfc4122());
        /**
         * @var array<\App\Entity\Commodity> $res
         */
        $res = $tst->getResult();
        $col = [];
        foreach ($res as $commodity) {
            $productId = $commodity->getProduct()->getId()->toBase32();
            $commodityTimeStamp = $commodity->getOperationTimestamp()->toBase32();
            // group by product
            $col[$productId][$commodityTimeStamp][] = $commodity;
            $count = (new CommodityCounter())->aggregate($commodity->getProduct()->getCommodities());
        }

        return new CommodityContainer($col);
    }

    public function findLastCommodities(): GroupDto
    {
        $sql = 'select cmd.related_order_id, cmd.commodity_source_id, cmd.operation_timestamp, cmd.amount, cmd.operation_type, cmd.id, cmd.product_id, o.history  from (select * from ((select distinct ON (c.related_order_id, c.commodity_source_id) related_order_id, c.commodity_source_id, c.operation_timestamp, c.amount, c.operation_type, c.id, c.product_id from commodity c
        where c.related_order_id IS NOT NULL
        order by c.related_order_id, c.commodity_source_id, c.operation_timestamp desc)
        union
        select related_order_id, commodity_source_id, operation_timestamp, amount, operation_type, id, product_id from commodity
        where related_order_id IS NULL) agg
        order by agg.id, agg.operation_timestamp) cmd
        left join "order" o on o.id = cmd.related_order_id';
        $conn = $this->getEntityManager()->getConnection();

        try {
            $result = $conn->executeQuery($sql);
            $w = $result->fetchAllAssociative();
            $aggregation = new GroupData(array_map(function ($e) use ($w) {
                return new CommodityDto(
                    relatedOrderId:     $e['related_order_id'],
                    commoditySourceId:  $e['commodity_source_id'],
                    operationTimestamp: $e['operation_timestamp'],
                    amount:             $e['amount'],
                    operationType:      $e['operation_type'],
                    id:                 $e['id'],
                    productId:          $e['product_id'],
                    history:            $e['history']
                );
            }, $w));

            return $aggregation->groupData();

        } catch (DbalException $exception) {
            return new GroupDto([]);
        }


//        01HNQTTXCMZ0WJ9J169TERM4TZ
//        {
//            "status": "CORRECTION",
//  "products": [
//    {
//        "product": "01HNN7V20094QEAKF9WVYVNEBN",
//      "amount": 1
//    }
//  ]
//}
//    /**
//     * @return Order[] Returns an array of Order objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('o')
//            ->andWhere('o.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('o.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Order
//    {
//        return $this->createQueryBuilder('o')
//            ->andWhere('o.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
    }
}
