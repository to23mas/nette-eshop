<?php declare(strict_types = 1);

namespace App\AdminModule\Components\ProductEditForm;

use App\Model\Entities\Product;
use App\Model\Facades\CategoriesFacade;
use App\Model\Facades\ProductsFacade;
use Nette;
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
		$this->getTemplate()->setFile(__DIR__ . '/templates/edit.latte');
		$this->getTemplate()->render();
	}

	public function createComponent(string $name): Form
	{
		$form = new Form;

		$form->addText('title')
			->setRequired('Musíte zadat název produktu')
			->setMaxLength(100);

		$form->addText('description');

		$form->addText('price')
			->setHtmlType('number')
			->addRule(Form::NUMERIC,'Musíte zadat číslo.')
			->setRequired('Musíte zadat cenu produktu');//tady by mohly být další kontroly pro min, max atp.

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

		$form->addSelect('categories', null, $this->findCategories())
			->setDefaultValue($this->product->category->categoryId)
			->setPrompt('--vyberte kategorii--')
			->setRequired(false);

		if ($this->product !== null) {
			$form->setValues([
				'title' => $this->product->title,
				'description' => $this->product->description,
				'price' => $this->product->price,
				'available' => $this->product->available,
				'url' => $this->product->url,
			]);
		}

		$form->addSubmit('submit', 'Save');
		$form->addSubmit('submitAndStay', 'Save and Stay');

		$form->onSuccess[] = [$this, 'handleFormSubmitted'];
		return $form;

			// //uložení fotky
			// if (($values['photo'] instanceof Nette\Http\FileUpload) && ($values['photo']->isOk())){
			// 	try{
			// 		$this->productsFacade->saveProductPhoto($values['photo'], $product);
			// 	}catch (\Exception $e){
			// 		$this->onFailed('Produkt byl uložen, ale nepodařilo se uložit jeho fotku.');
			// 	}
			// }
			//
	}

	public function handleFormSubmitted(Form $form, ProductFormData $formData): void
	{
		$product = new Product;

		$product->title = $formData->title;
		$product->description = $formData->description;
		$product->url = $formData->url;
		$product->price = (float) $formData->price;
		$product->available = $formData->available;
		$product->category = $this->categoriesFacade->getCategory($formData->categories);
		$product->photoExtension = $formData->photo->getImageFileExtension();

		$this->productsFacade->saveProduct($product);

		if (($formData->photo instanceof FileUpload) && ($formData->photo->isOk())){
			try {
				$this->productsFacade->saveProductPhoto($formData->photo, $product);
			} catch (\Exception $e){
				bdump($e);
				// $this->onFailed('Produkt byl uložen, ale nepodařilo se uložit jeho fotku.');
			}
		}
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
