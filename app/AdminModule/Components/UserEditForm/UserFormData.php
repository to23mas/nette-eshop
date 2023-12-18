<?php declare(strict_types=1);

namespace App\AdminModule\Components\UserEditForm;

final class UserFormData
{

	public function __construct(
		public string $username,
		public string $email,
		public string $role,
	) {}

}

