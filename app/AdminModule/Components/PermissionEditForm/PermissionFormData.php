<?php declare(strict_types=1);

namespace App\AdminModule\Components\PermissionEditForm;

final class PermissionFormData
{

	public function __construct(
		public string $roleId,
		public string $resourceId,
		public string $action,
		public string $type,
	) {}
}
