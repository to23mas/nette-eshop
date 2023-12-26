<?php declare(strict_types=1);

namespace App\AdminModule\Components\ResourceEditForm;

final class ResourceFormData {

	public function __construct(
		public string $resourceId,
	) {}
}

