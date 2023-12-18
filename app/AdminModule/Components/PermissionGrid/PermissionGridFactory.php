<?php declare(strict_types=1);

namespace App\AdminModule\Components\PermissionGrid;

use App\Model\Entities\Role;

interface PermissionGridFactory {

  public function create(?Role $role): PermissionGrid;
}

