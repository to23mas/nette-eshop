<?php declare(strict_types=1);

namespace App\AdminModule\Components\ProductGrid;

use App\Model\Facades\CategoriesFacade;
use App\Model\Facades\ProductsFacade;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;

final class	ProductGrid extends Control
{

	public function __construct(
		private ?array $searchParams,
		private readonly ProductsFacade $productsFacade,
		private readonly CategoriesFacade $categoriesFacade,
	) {}

	public function render(): void
	{
		if ($this->searchParams === null) {
			$this->getTemplate()->products =  $this->productsFacade->findProducts(['order' => 'title']);
		} else {
			$this->getTemplate()->products =  $this->productsFacade->findProducts($this->searchParams);
		}

		$this->getTemplate()->setFile(__DIR__ . '/templates/grid.latte');
		$this->getTemplate()->render();
	}

	public function createComponent(string $name): Form
	{
		$form = new Form;

		$form->addText('title')->setNullable();

		$form->addSelect('available', null, [
			1 => '✔️',
			0 => '❌',
		])->setPrompt('vše');

		$form->addSelect('categories', null, $this->findCategories())
			->setPrompt('vše');

		$form->addSubmit('search', '');
		$form->addSubmit('clear', '');

		if ($this->searchParams !== null) {
			$form->setDefaults([
				'available' => $this->searchParams['available'],
				'categories' => $this->searchParams['category_id'],
				'title' => $this->searchParams['title'],
			]);
		}

		$form->onSuccess[] = [$this, 'handleFormSubmitted'];
		return $form;
	}

	public function handleFormSubmitted(Form $form, ProductFormData $formData): void
	{
		/** @var SubmitButton $search */
		$search = $form['search'];

		$searchParams = [];
		$searchParams['order'] = 'title';
		if ($search->isSubmittedBy()) { // search
			if ($formData->available !== null) {
				$searchParams['available'] = $formData->available;
			};
			if ($formData->categories !== null) {
				$searchParams['category_id'] = $formData->categories;
			};
			if ($formData->title !== null) {
				$searchParams['title'] = $formData->title;
			};
			$this->getTemplate()->products =  $this->productsFacade->findProducts($searchParams);
			$this->presenter->redirect('default', ['searchParams' => $searchParams]);
		} else { // clear
			$this->presenter->redirect('default');
		}
	}

	public function handleEdit(int $productId): void
	{
		$this->presenter->redirect('Product:edit', ['productId' => $productId]);
	}

	public function handleDelete(int $productId): void {
		try {
			$this->productsFacade->delete($this->productsFacade->getProduct($productId));
			$this->presenter->flashMessage('Product úspěšně smazána', 'info');
		} catch (\Throwable) {
			$this->presenter->flashMessage('Nepodařilo se smazat product', 'danger');
		}

		$this->presenter->redirect('Product:default');
	}

	private function findCategories(): array
	{
		$categoriesIds = [];
		$allCat = $this->categoriesFacade->findCategories();

		foreach ($allCat as $cat) {
			$categoriesIds[$cat->categoryId] = $cat->title;
		}

		return $categoriesIds;
	}
}

