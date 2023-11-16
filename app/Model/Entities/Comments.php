<?php

namespace App\Model\Entities;


use Dibi\DateTime;
use LeanMapper\Entity;

/**
 * Class Category
 * @package App\Model\Entities
 * @property int $commentsId
 * @property Product $product m:hasOne
 * @property string $name
 * @property string $content
 * @property string $created
 * @property int $likes
 * @property int $dislikes
 * @property LikedBy[] $likedBy m:belongsToMany
 */
class Comments extends Entity
{

}