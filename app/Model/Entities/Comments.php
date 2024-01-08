<?php

namespace App\Model\Entities;


use Dibi\DateTime;
use LeanMapper\Entity;

/**
 * Class Category
 * @package App\Model\Entities
 * @property int $commentsId
 * @property Product $product m:hasOne
 * @property int $userId
 * @property string $name
 * @property string $content
 * @property string $created
 * @property int $likes
 * @property int $dislikes
 */
class Comments extends Entity
{

}