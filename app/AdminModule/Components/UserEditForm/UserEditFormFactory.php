<?php declare(strict_types=1);

namespace App\AdminModule\Components\UserEditForm;

use App\Model\Entities\User;

interface UserEditFormFactory {

  public function create(?User $user): UserEditForm;
}
