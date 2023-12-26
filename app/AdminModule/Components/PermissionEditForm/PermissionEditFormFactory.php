<?php declare(strict_types=1);

namespace App\AdminModule\Components\PermissionEditForm;

use App\Model\Entities\Permission;
use App\Model\Entities\Role;

interface PermissionEditFormFactory {

  public function create(?Permission $permission, ?Role $role): PermissionEditForm;
}

