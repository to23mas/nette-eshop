<?php

namespace App\Model\Entities;
use LeanMapper\Entity;


/**
 * Class Category
 * @package App\Model\Entities
 * @property int $likedById
 * @property Comments $comments m:hasOne
 * @property User $user m:hasOne
 */
class LikedBy extends Entity
{

}