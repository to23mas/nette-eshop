<?php declare(strict_types=1);

namespace App\AdminModule\Components\CategoryEditForm;

final class CategoryFormData
{

	public function __construct(
		public string $title,
		public string $description,
	) {}
}
