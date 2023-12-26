<?php declare(strict_types=1);

namespace App\AdminModule\Components\ProductEditForm;

use App\Model\Entities\Product;

interface ProductEditFormFactory
{

  public function create(?Product $product = null): ProductEditForm;
}
