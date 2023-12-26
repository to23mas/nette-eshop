<?php declare(strict_types=1);

namespace App\AdminModule\Components\CategoryGrid;

use App\Model\Entities\Role;

interface CategoryGridFactory {

  public function create(): CategoryGrid;
}
