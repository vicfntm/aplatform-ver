<?php

namespace App\Enums;

enum CommodityOperationType
{
    case SOLD;
    case IMPORT;
    case PREORDER;
    case RETURN;
    case CORRECTION;
}
