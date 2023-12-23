<?php declare(strict_types=1);

namespace App\AdminModule\Presenters;

use App\AdminModule\Components\ProductEditForm\ProductEditForm;
use App\AdminModule\Components\ProductEditForm\ProductEditFormFactory;
use App\AdminModule\Components\ProductGrid\ProductGrid;
use App\AdminModule\Components\ProductGrid\ProductsGridFactory;
use App\Model\Entities\Product;
use App\Model\Facades\CategoriesFacade;
use App\Model\Facades\ProductsFacade;

class ProductPresenter extends BasePresenter
{

	public ?Product $product = null;

	public ?int $selectedCategory = null;

	public function __construct(
		public ProductsGridFactory $productGridFactory,
		public ProductsFacade $productsFacade,
		public ProductEditFormFactory $productEditFormFactory,
		public CategoriesFacade $categoriesFacade,
	) {}

	public function renderDefault(?int $selectedCategory = null): void
	{
		$this->selectedCategory = $selectedCategory;
	}

	public function actionEdit(?int $productId):void
	{
		if ($productId !== null) {
			try {
				$this->product = $this->productsFacade->getProduct($productId);
			} catch (\Exception $e) {
				$this->flashMessage('Požadovaný produkt nebyl nalezen.', 'error');
				$this->redirect('default');
			}
		}
		//not sure about this condition TODO
		// if (!$this->user->isAllowed($product,'edit')) {
		// 	$this->flashMessage('Požadovaný produkt nemůžete upravovat.', 'error');
		// 	$this->redirect('default');
		// }
	}

	protected function createComponentProductEditForm(): ProductEditForm
	{
		return $this->productEditFormFactory->create($this->product);
	}

	protected function createComponentProductGrid(): ProductGrid
	{
		return $this->productGridFactory->create($this->selectedCategory);
	}
}
