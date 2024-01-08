<?php

namespace App\Model\Entities;


use Dibi\DateTime;
use LeanMapper\Entity;

/**
 * Class Category
 * @package App\Model\Entities
 * @property int $sizeId
 * @property Product $product m:hasOne
 * @property int $size
 * @property int $quantity
 */
class Size extends Entity
{

}