<?php declare(strict_types=1);

namespace App\AdminModule\Components\ProductGrid;

interface ProductsGridFactory {

  public function create(?int $categoryId = null): ProductGrid;

}
