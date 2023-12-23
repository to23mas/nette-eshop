<?php declare(strict_types=1);

namespace App\AdminModule\Components\CategoryEditForm;

use App\Model\Entities\Category;

interface CategoryEditFormFactory {

	public function create(?Category $category): CategoryEditForm;

}
