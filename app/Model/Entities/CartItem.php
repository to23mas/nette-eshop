<?php


namespace App\Model\Entities;


use LeanMapper\Entity;

/**
 * Class CartItem
 * @package App\Model\Entities
 * @property int $cartItemId
 * @property Product $product m:hasOne
 * @property Cart|null $cart m:hasOne
 * @property int $count = 0
 * @property int $size
 * @property Objednavka $objednavka m:hasOne
 */
class CartItem extends Entity{

}