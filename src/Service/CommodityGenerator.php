<?php

declare(strict_types=1);


namespace App\Service;


use App\DTO\CommodityDto;
use App\DTO\ProductDto;
use App\Enums\CommodityOperationType;
use App\Exception\NotEnoughStockException;
use Symfony\Component\Uid\Ulid;

final readonly class CommodityGenerator
{
    private ?string $status;

    /**
     * @param \App\DTO\ProductDto $productToUpdate
     * @param array<CommodityDto> $transactions
     * @param array<CommodityDto> $imports
     * @param ?string $orderId
     * @param ?string $status
     */
    public function __construct(
        private ProductDto $productToUpdate,
        private array $transactions,
        private array $imports,
        private ?string $orderId,
        ?string $status = 'PREORDER',
    ) {
            $this->status = $status;
    }

    /**
     * @throws \App\Exception\NotEnoughStockException
     * @return array<CommodityDto>
     */
    public function generateCommodities(): array
    {
        $analyzer = new CommodityStockAnalyzer(transactionsByProduct: [$this->transactions]);
        $debt = $analyzer->prepareCalculation()->sum();
        $importAnalyzer = new CommodityStockAnalyzer(transactionsByProduct: [$this->imports]);
        $credit = $importAnalyzer->prepareCalculation()->sum();
        $totalAvailStock = $credit + $debt;
        $requestedAmount = $this->productToUpdate->getAmount();

        if ($requestedAmount > $totalAvailStock) {
            throw new NotEnoughStockException();
        }

        $cmdSet = [];
        $opTimeStamp = (new Ulid())->toRfc4122();

        foreach ($this->imports as $import) {
            $cmd = $this->findImportStock($import);

            $sold = (new CommodityStockAnalyzer(transactionsByProduct: [$cmd]))->prepareCalculation()->sum();

            $currentStock = $import->getAmount() + $sold;

            if ($currentStock >= $requestedAmount) {
                $cD = new CommodityDto(
                    relatedOrderId:     $this->orderId,
                    commoditySourceId:  $import->getId(),
                    operationTimestamp: $opTimeStamp,
                    amount:             $requestedAmount,
                    operationType:      $this->status,
                    id:                 (new Ulid())->toRfc4122(),
                    productId:          $this->productToUpdate->getIdAsRfc(),
                    history:            '[]'
                );
                $cmdSet[] = $cD;

                break;
            } else {
                $requestedAmount -= $currentStock;
                $cD = new CommodityDto(
                    relatedOrderId:     $this->orderId,
                    commoditySourceId:  $import->getId(),
                    operationTimestamp: $opTimeStamp,
                    amount:             $currentStock,
                    operationType:      CommodityOperationType::PREORDER->name,
                    id:                 (new Ulid())->toRfc4122(),
                    productId:          $this->productToUpdate->getIdAsRfc(),
                    history:            '[]'
                );
                $cmdSet[] = $cD;

            }



        }

        return $cmdSet;
    }

    private function findImportStock($importSet): array
    {
        $importCommodityId = $importSet->getId();

        return array_filter($this->transactions, function ($w) use ($importCommodityId) {
            return $w->getCommoditySourceId() === $importCommodityId;
        });
    }


}