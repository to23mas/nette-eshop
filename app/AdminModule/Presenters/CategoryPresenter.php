<?php declare(strict_types=1);

namespace App\AdminModule\Presenters;

use App\AdminModule\Components\CategoryEditForm\CategoryEditForm;
use App\AdminModule\Components\CategoryEditForm\CategoryEditFormFactory;
use App\AdminModule\Components\CategoryGrid\CategoryGrid;
use App\AdminModule\Components\CategoryGrid\CategoryGridFactory;
use App\Model\Entities\Category;
use App\Model\Facades\CategoriesFacade;
use Nette\DI\Attributes\Inject;

final class CategoryPresenter extends BasePresenter {

	#[Inject]
	public CategoriesFacade $categoriesFacade;

	#[Inject]
	public CategoryEditFormFactory $categoryEditFormFactory;

	#[Inject]
	public CategoryGridFactory $categoryGrid;

	public ?Category $category = null;

	public function renderDefault(): void {
		$this->template->categories=$this->categoriesFacade->findCategories(['order'=>'title']);
	}

	public function actionEdit(?int $categoryId):void {
		if ($categoryId !== null) {
			try{
				$this->category = $this->categoriesFacade->getCategory($categoryId);
			} catch (\Throwable) {
				$this->flashMessage('Požadovaná kategorie nebyla nalezena.', 'error');
				$this->redirect('default');
			}
		}
	}

	protected function createComponentCategoriesGrid(): CategoryGrid
	{
		return $this->categoryGrid->create();
	}

	public function createComponentCategoriesEditForm(): CategoryEditForm
	{
		return $this->categoryEditFormFactory->create($this->category);
	}
}
