<?php declare(strict_types=1);

namespace App\AdminModule\Components\ProductPhotoForm;

use Nette\Http\FileUpload;

final class ProductFormData {

	public function __construct(public FileUpload $photo) {}
}

