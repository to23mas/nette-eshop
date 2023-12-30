<?php declare(strict_types = 1);

namespace App\AdminModule\Components\ProductEditForm;

use App\Model\Entities\Product;
use App\Model\Facades\CategoriesFacade;
use App\Model\Facades\ProductsFacade;
use Nette;
use Nette\Application\Attributes\CrossOrigin;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Forms\Controls\TextInput;
use Nette\Forms\Controls\UploadControl;
use Nette\Http\FileUpload;
use Nette\Utils\Strings;

class ProductEditForm extends Control
{

	public function __construct(
		private ?Product $product,
		private readonly CategoriesFacade $categoriesFacade,
		private readonly ProductsFacade $productsFacade,
	) { }

	public function render(): void
	{
		$this->getTemplate()->product = $this->product;
		$this->getTemplate()->setFile(__DIR__ . '/templates/edit.latte');
		$this->getTemplate()->render();
	}

	public function createComponent(string $name): Form
	{
		$form = new Form;

		$form->addText('title')
			->setRequired('Musíte zadat název produktu')
			->setMaxLength(100);

		$form->addText('description')->setNullable();

		$form->addText('price')
			->setHtmlType('number')
			->addRule(Form::NUMERIC,'Musíte zadat číslo.')
			->setRequired('Musíte zadat cenu produktu');//tady by mohly být další kontroly pro min, max atp.

		$form->addCheckbox('available');

		$form->addText('url')
			->setMaxLength(100)
			->addFilter(function(string $url){
				return Strings::webalize($url);
			});

		$form->addSelect('categories', null, $this->findCategories())
			->setDefaultValue($this->product->category->categoryId)
			->setPrompt('--vyberte kategorii--');

		$form->setValues([
			'title' => $this->product->title,
			'description' => $this->product->description,
			'price' => $this->product->price,
			'available' => $this->product->available,
			'url' => $this->product->url,
		]);

		$form->addSubmit('submit', 'Save');
		$form->addSubmit('submitAndStay', 'Save and Stay');


		$form->onSuccess[] = [$this, 'handleFormSubmitted'];
		return $form;
	}

	public function handleFormSubmitted(Form $form, EditFormData $formData): void
	{
		$this->product->title = $formData->title;
		$this->product->description = $formData->description;
		$this->product->url = $formData->url;
		$this->product->price = (float) $formData->price;
		$this->product->available = $formData->available;
		$this->product->category = $formData->categories === null ? null : $this->categoriesFacade->getCategory($formData->categories);

		try {
			$this->productsFacade->saveProduct($this->product);
			$this->presenter->flashMessage('Produkt upraven', 'info');
		} catch (\Throwable) {
			$this->presenter->flashMessage('Nepodařilo se upravit produkt', 'danger');
		}

		if (($formData->photo instanceof FileUpload) && ($formData->photo->isOk())){
			try {
				$this->productsFacade->saveProductPhoto($formData->photo, $product);
			} catch (\Throwable $e){
				$this->presenter->flashMessage('Produkt byl uložen, ale nepodařilo se uložit jeho fotku.', 'danger');
			}
		}

		/** @var SubmitButton $submitAndStay */
		$submitAndStay = $form['submitAndStay'];

		$submitAndStay->isSubmittedBy()
		? $this->presenter->redirect('Product:edit', ['productId' => $this->product->productId ?? $product->productId])
		: $this->presenter->redirect('Product:default');
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

	public function handleEditPhoto(?int $productId): void {
		$this->presenter->redirect('Product:editPhoto', ['productId' => $productId]);
	}
}
