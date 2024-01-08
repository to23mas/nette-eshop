<?php declare(strict_types=1);

namespace App\AdminModule\Components\ProductEditForm;

use Nette\Http\FileUpload;

final class ProductFormData {

	public function __construct(
		public string $title,
		public string $description,
		public string $price,
		public FileUpload $photo,
		public bool $available,
		public string $url,
		public ?int $categories, //category ID
	) {}
}


