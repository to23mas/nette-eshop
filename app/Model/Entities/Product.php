<?php

namespace App\Model\Entities;

use LeanMapper\Entity;

/**
 * Class Product
 * @package App\Model\Entities
 * @property int $productId
 * @property string $title
 * @property string $url
 * @property string $description
 * @property float $price
 * @property string $photoExtension = ''
 * @property bool $available = true
 * @property Category|null $category m:hasOne
 * @property Comments[] $comments m:belongsToMany
 * @property Size[] $size m:belongsToMany
 * @property string $brand
 * @property string $color
 * @property string $type
 * @property string $modelNumber
 */
class Product extends Entity implements \Nette\Security\Resource{

  /**
   * @inheritDoc
   */
  function getResourceId():string{
    return 'Product';
  }
}