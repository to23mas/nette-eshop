<?php declare(strict_types=1);

namespace App\AdminModule\Components\CategoryEditForm;

use App\Model\Entities\Category;
use App\Model\Facades\CategoriesFacade;
use Nette;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;

class CategoryEditForm extends Control
{

	public function __construct(
		private ?Category $category,
		private readonly CategoriesFacade $categoriesFacade,
	) {}


	public function render(): void
	{
		$this->getTemplate()->setFile(__DIR__ . '/templates/edit.latte');
		$this->getTemplate()->render();
	}

	public function createComponent(string $name): Form
	{
		$form = new Form;

		$form->addText('title')
			->setRequired('Poviné pole');

		$form->addText('description');

		if ($this->category !== null) {
			$form->addText('categoryId')->setDisabled();
			$form->setDefaults([
				'categoryId' => $this->category->categoryId,
				'title' => $this->category->title,
				'description' => $this->category->description,
			]);
		}

		$form->addSubmit('submit', 'Save');
		$form->addSubmit('submitAndStay', 'Save and Stay');

		$form->onSuccess[] = [$this, 'handleFormSubmitted'];
		return $form;
	}

	public function handleFormSubmitted(Form $form, CategoryFormData $formData): void
	{
		if ($this->category === null) { // create
			try {
				$this->categoriesFacade->create($formData->title, $formData->description);
				$this->presenter->flashMessage('Kategorie vytvořena', 'info');
			} catch (\Throwable $e) {
				$this->presenter->flashMessage('Kategorii nebylo možné vytvořit', 'danger');
				return;
			}
		} else { // edit
			try {
				$this->category->title = $formData->title;
				$this->category->description = $formData->description;

				$this->categoriesFacade->saveCategory($this->category);
				$this->presenter->flashMessage('Kategorie upravena', 'info');
			} catch (\Throwable) {
				$this->presenter->flashMessage('Kategorii nebylo možné upravit', 'danger');
				return;
			}
		}

		/** @var SubmitButton $submitAndStay */
		$submitAndStay = $form['submitAndStay'];

		$submitAndStay->isSubmittedBy()
			? $this->presenter->redirect('Category:edit', ['categoryId' => $this->categoriesFacade->getIdByTitle($formData->title)])
			: $this->presenter->redirect('Category:default');
	}
}
