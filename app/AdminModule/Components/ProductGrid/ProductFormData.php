<?php declare(strict_types=1);

namespace App\AdminModule\Components\ProductGrid;

final class ProductFormData {

	public function __construct(
		public ?int $available,
		public ?int $categories,
		public ?string $title,
	) {}
}

