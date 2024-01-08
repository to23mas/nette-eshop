<?php declare(strict_types=1);

namespace App\AdminModule\Components\ProductPhotoForm;

use App\Model\Entities\Product;

interface ProductPhotoFormFactory
{

	public function create(?Product $product = null): ProductPhotoForm;
}
