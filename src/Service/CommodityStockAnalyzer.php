<?php

declare(strict_types=1);


namespace App\Service;


use App\DTO\CalcData;
use App\DTO\CalculatorComposite;
use App\DTO\CommodityCalculatorDto;
use App\DTO\CommodityDto;
use App\Enums\CommodityOperationType;

/**
 * create class composite which contain child entities represents commodities ready for calculation (define amount sign per commodity)
 */
final readonly class CommodityStockAnalyzer
{

    public function __construct(private iterable $transactionsByProduct)
    {
    }

    public function prepareCalculation(): CalculatorComposite
    {
        $incrementTypes = [CommodityOperationType::RETURN->name, CommodityOperationType::IMPORT->name];
        $calc = new CommodityCalculatorDto();
        /**
         * @var iterable<CommodityDto> $transactionSet
         */
        foreach ($this->transactionsByProduct as $transactionSet) {
            foreach ($transactionSet as $commodity) {
                if (in_array($commodity->getOperationType(), $incrementTypes)) {
                    $calc->add(
                        new CalcData(
//                            commoditySourceId: $commodity->getCommoditySourceId(),
                            amount:            $commodity->getAmount()
                        )
                    );
                } else {
                    $calc->add(
                        new CalcData(
//                            commoditySourceId: $commodity->getCommoditySourceId(),
                            amount:            -$commodity->getAmount()
                        )
                    );
                }
            }
        }

        return $calc;
    }

}