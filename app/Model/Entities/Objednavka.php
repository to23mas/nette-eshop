<?php

namespace App\Model\Entities;

use LeanMapper\Entity;

/**
 * Class Category
 * @package App\Model\Entities
 * @property int|null $objednavkaId * @property
 * @property string $jmeno
 * @property string $email
 * @property string $zprava
 * @property string $created
 * @property User $user m:hasOne
 * @property CartItem[] $cartItems m:belongsToMany
 * @property int $cena
 * @property string|null $stav
 */

class Objednavka extends Entity
{

}