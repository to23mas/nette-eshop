<?php declare(strict_types=1);

namespace App\AdminModule\Presenters;

use App\AdminModule\Components\ProductEditForm\ProductAddForm;
use App\AdminModule\Components\ProductEditForm\ProductAddFormFactory;
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

	public ?array $searchParams = null;

	public function __construct(
		public ProductsGridFactory $productGridFactory,
		public ProductsFacade $productsFacade,
		public ProductEditFormFactory $productEditFormFactory,
		public ProductAddFormFactory $productAddFormFactory,
		public CategoriesFacade $categoriesFacade,
	) {}

	public function renderDefault(?array $searchParams = null): void
	{
		$this->searchParams = $searchParams;
	}

	public function actionAdd():void {}

	public function actionEdit(?int $productId):void
	{
		if ($productId !== null) {
			try {
				$this->product = $this->productsFacade->getProduct($productId);
			} catch (\Throwable) {
				$this->flashMessage('Požadovaný produkt nebyl nalezen.', 'error');
				$this->redirect('default');
			}
		}
	}

	protected function createComponentProductAddForm(): ProductAddForm
	{
		return $this->productAddFormFactory->create();
	}

	protected function createComponentProductEditForm(): ProductEditForm
	{
		return $this->productEditFormFactory->create($this->product);
	}

	protected function createComponentProductGrid(): ProductGrid
	{
		return $this->productGridFactory->create($this->searchParams);
	}
}
