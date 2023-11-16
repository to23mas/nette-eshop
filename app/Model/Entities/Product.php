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
 * @property string $brand
 * @property string $color
 * @property string $cut
 * @property string $model
 * @property int|null $thirtyeight
 * @property int|null $thirtynine
 * @property int|null $forty
 * @property int|null $fortyone
 * @property int|null $fortytwo
 * @property int|null $fortythree
 * @property int|null $fortyfour
 * @property int|null $fortyfive
 */
class Product extends Entity implements \Nette\Security\Resource{

    /**
     * @inheritDoc
     */
    function getResourceId():string{
        return 'Product';
    }
}