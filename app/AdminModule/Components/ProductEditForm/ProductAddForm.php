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

class ProductAddForm extends Control
{

	public function __construct(
		private readonly CategoriesFacade $categoriesFacade,
		private readonly ProductsFacade $productsFacade,
	) { }

	public function render(): void
	{
		$this->getTemplate()->setFile(__DIR__ . '/templates/add.latte');
		$this->getTemplate()->render();
	}

	public function createComponent(string $name): Form
	{
		$form = new Form;

		$form->addText('title')
			->setRequired('Musíte zadat název produktu')
			->setMaxLength(100);

		$form->addText('description')->setRequired();
		$form->addText('brand')->setRequired();
		$form->addText('color')->setRequired();
		$form->addText('type')->setRequired();
		$form->addText('modelNumber')->setRequired();

		$form->addText('price')
			->setHtmlType('number')
			->addRule(Form::NUMERIC,'Musíte zadat číslo.')
			->setRequired('Musíte zadat cenu produktu');//tady by mohly být další kontroly pro min, max atp.

		$form->addCheckbox('available');

		$form->addText('url')
			->setMaxLength(100)
			->addFilter(function(string $url){
				return Strings::webalize($url);
			})
			->addRule(function(TextInput $input) use ($productId) {
				try {
					$existingProduct = $this->productsFacade->getProductByUrl($input->value);
					return $existingProduct->productId==$productId->value;
				} catch (\Throwable) {
					return true;
				}
		},'Zvolená URL je již obsazena jiným produktem');

		$form->addUpload('photo')
			->setRequired('Pro uložení nového produktu je nutné nahrát jeho fotku.')
			->addRule(Form::MAX_FILE_SIZE, 'Nahraný soubor je příliš velký', 1000000)
			->addCondition(Form::FILLED)
			->addRule(function(UploadControl $photoUpload) {
				$uploadedFile = $photoUpload->value;
				if ($uploadedFile instanceof FileUpload) {
					$extension=strtolower($uploadedFile->getImageFileExtension());
					return in_array($extension,['jpg','jpeg','png']);
				}
				return false;
				},'Je nutné nahrát obrázek ve formátu JPEG či PNG.');

		$form->addSelect('categories', null, $this->findCategories())
			->setPrompt('--vyberte kategorii--')
			->setRequired(false);

		$form->addSubmit('submit', 'Uložit');
		$form->addSubmit('submitAndStay', 'Uložit a zůstat');

		$form->onSuccess[] = [$this, 'handleFormSubmitted'];
		return $form;
	}

	public function handleFormSubmitted(Form $form, ProductFormData $formData): void
	{
		$product = new Product;

		$product->title = $formData->title;
		$product->description = $formData->description;
		$product->url = $formData->url;
		$product->price = (float) $formData->price;
		$product->available = $formData->available;
		$product->brand = $formData->brand;
		$product->color = $formData->color;
		$product->type = $formData->type;
		$product->modelNumber = $formData->modelNumber;
		$product->category = $formData->categories === null ?  null : $this->categoriesFacade->getCategory($formData->categories);
		$product->photoExtension = $formData->photo->getImageFileExtension();

		try {
			$this->productsFacade->saveProduct($product);
			$this->presenter->flashMessage('Produkt vytvořen', 'info');
		} catch (\Throwable $e) {
			bdump($e);
			$this->presenter->flashMessage('Nepodařilo se vytvořit produkt', 'danger');
			return;
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
		? $this->presenter->redirect('Product:edit', ['productId' => $product->productId])
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
}
