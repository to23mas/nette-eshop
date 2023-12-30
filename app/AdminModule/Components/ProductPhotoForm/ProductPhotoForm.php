<?php declare(strict_types = 1);

namespace App\AdminModule\Components\ProductPhotoForm;

use App\Model\Entities\Product;
use App\Model\Facades\ProductsFacade;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\TextInput;
use Nette\Forms\Controls\UploadControl;
use Nette\Http\FileUpload;
use Nette\Utils\Strings;

class ProductPhotoForm extends Control
{

	public function __construct(
		private ?Product $product,
		private readonly ProductsFacade $productsFacade,
	) { }

	public function render(): void
	{
		$this->getTemplate()->product = $this->product;
		$this->getTemplate()->setFile(__DIR__ . '/templates/form.latte');
		$this->getTemplate()->render();
	}

	public function createComponent(string $name): Form
	{
		$form = new Form;

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

		$form->addSubmit('submit', 'Uložit');

		$form->onSuccess[] = [$this, 'handleFormSubmitted'];
		return $form;
	}

	public function handleFormSubmitted(Form $form, ProductFormData $formData): void
	{
		if (($formData->photo instanceof FileUpload) && ($formData->photo->isOk())){
			try {
				$this->productsFacade->saveProductPhoto($formData->photo, $this->product);
			} catch (\Throwable){
				$this->presenter->flashMessage('Fotku se nepodařilo změnit', 'danger');
			}
		}

		$this->product->photoExtension = $formData->photo->getImageFileExtension();
		$this->productsFacade->saveProduct($this->product);

		 $this->presenter->redirect('Product:edit', ['productId' => $this->product->productId]);

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

