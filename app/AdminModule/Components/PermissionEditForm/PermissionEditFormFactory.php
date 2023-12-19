<?php declare(strict_types=1);

namespace App\AdminModule\Components\PermissionEditForm;

use App\Model\Entities\Permission;

interface PermissionEditFormFactory {

  public function create(?Permission $permission): PermissionEditForm;
}

