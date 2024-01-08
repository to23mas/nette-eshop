<?php declare(strict_types=1);

namespace App\AdminModule\Components\ProductEditForm;

final class EditFormData {

	public function __construct(
		public string $title,
		public ?string $description,
		public string $price,
		public bool $available,
		public string $url,
		public ?int $categories, //category ID
	) {}
}



