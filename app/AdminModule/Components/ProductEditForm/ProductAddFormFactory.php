<?php declare(strict_types=1);

namespace App\AdminModule\Components\ProductEditForm;

interface ProductAddFormFactory
{

  public function create(): ProductAddForm;
}
