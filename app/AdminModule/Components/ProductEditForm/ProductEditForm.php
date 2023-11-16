<?php

namespace App\AdminModule\Components\ProductEditForm;

use App\Model\Entities\Category;
use App\Model\Entities\Product;
use App\Model\Facades\CategoriesFacade;
use App\Model\Facades\ProductsFacade;
use Nette;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\SmartObject;
use Nextras\FormsRendering\Renderers\Bs4FormRenderer;
use Nextras\FormsRendering\Renderers\FormLayout;

/**
 * Class ProductEditForm
 * @package App\AdminModule\Components\ProductEditForm
 *
 * @method onFinished(string $message = '')
 * @method onFailed(string $message = '')
 * @method onCancel()
 */
class ProductEditForm extends Form{

  use SmartObject;

  /** @var callable[] $onFinished */
  public $onFinished = [];
  /** @var callable[] $onFailed */
  public $onFailed = [];
  /** @var callable[] $onCancel */
  public $onCancel = [];
  /** @var CategoriesFacade */
  private $categoriesFacade;
  /** @var ProductsFacade $productsFacade */
  private $productsFacade;

  /**
   * TagEditForm constructor.
   * @param Nette\ComponentModel\IContainer|null $parent
   * @param string|null $name
   * @param ProductsFacade $productsFacade
   * @noinspection PhpOptionalBeforeRequiredParametersInspection
   */
  public function __construct(Nette\ComponentModel\IContainer $parent = null, string $name = null, CategoriesFacade $categoriesFacade, ProductsFacade $productsFacade){
    parent::__construct($parent, $name);
    $this->setRenderer(new Bs4FormRenderer(FormLayout::VERTICAL));
    $this->categoriesFacade=$categoriesFacade;
    $this->productsFacade=$productsFacade;
    $this->createSubcomponents();
  }

  private function createSubcomponents(){
    $productId=$this->addHidden('productId');
    $this->addText('title','Název produktu')
      ->setRequired('Musíte zadat název produktu')
      ->setMaxLength(100);

    $this->addText('url','URL produktu')
      ->setMaxLength(100)
      ->addFilter(function(string $url){
        return Nette\Utils\Strings::webalize($url);
      })
      ->addRule(function(Nette\Forms\Controls\TextInput $input)use($productId){
        try{
          $existingProduct = $this->productsFacade->getProductByUrl($input->value);
          return $existingProduct->productId==$productId->value;
        }catch (\Exception $e){
          return true;
        }
      },'Zvolená URL je již obsazena jiným produktem');

    #region kategorie
    $categories=$this->categoriesFacade->findCategories();
    $categoriesArr=[];
    foreach ($categories as $category){
      $categoriesArr[$category->categoryId]=$category->title;
    }
    $this->addSelect('categoryId','Kategorie',$categoriesArr)
      ->setPrompt('--vyberte kategorii--')
      ->setRequired(false);
    #endregion kategorie

    $this->addTextArea('description', 'Popis produktu')
      ->setRequired('Zadejte popis produktu.');

      $this->addText('brand', 'Značka:')
          ->setMaxLength(100)
          ->setRequired('Zadejte značku produktu.');

      $this->addText('color', 'Barva:')
          ->setMaxLength(100)
          ->setRequired('Zadejte barvu produktu.');

      $this->addText('cut', 'Střih:')
          ->setMaxLength(100)
          ->setRequired('Zadejte střih produktu.');

      $this->addText('model', 'Model:')
          ->setMaxLength(100)
          ->setRequired('Zadejte číslo modelu produktu.');

    $this->addText('price', 'Cena')
      ->setHtmlType('number')
      ->addRule(Form::NUMERIC)
      ->setRequired('Musíte zadat cenu produktu');//tady by mohly být další kontroly pro min, max atp.

    $this->addText('thirtyeight', 'Velikost: 38')
      ->setHtmlType('number')
      ->addRule(Form::NUMERIC)
      ->setRequired('Musíte vědět kolik bot velikosti 38 máme!');//tady by mohly být další kontroly pro min, max atp.

    $this->addText('thirtynine', 'Velikost: 39')
      ->setHtmlType('number')
      ->addRule(Form::NUMERIC)
      ->setRequired('Musíte vědět kolik bot velikosti 39 máme!');//tady by mohly být další kontroly pro min, max atp.

      $this->addText('forty', 'Velikost: 40')
          ->setHtmlType('number')
          ->addRule(Form::NUMERIC)
          ->setRequired('Musíte vědět kolik bot velikosti 40 máme!');//tady by mohly být další kontroly pro min, max atp.

      $this->addText('fortyone', 'Velikost: 41')
          ->setHtmlType('number')
          ->addRule(Form::NUMERIC)
          ->setRequired('Musíte vědět kolik bot velikosti 41 máme!');//tady by mohly být další kontroly pro min, max atp.

      $this->addText('fortytwo', 'Velikost: 42')
          ->setHtmlType('number')
          ->addRule(Form::NUMERIC)
          ->setRequired('Musíte vědět kolik bot velikosti 42 máme!');//tady by mohly být další kontroly pro min, max atp.

      $this->addText('fortythree', 'Velikost: 43')
          ->setHtmlType('number')
          ->addRule(Form::NUMERIC)
          ->setRequired('Musíte vědět kolik bot velikosti 43 máme!');//tady by mohly být další kontroly pro min, max atp.

      $this->addText('fortyfour', 'Velikost: 44')
          ->setHtmlType('number')
          ->addRule(Form::NUMERIC)
          ->setRequired('Musíte vědět kolik bot velikosti 44 máme!');//tady by mohly být další kontroly pro min, max atp.

      $this->addText('fortyfive', 'Velikost: 45')
          ->setHtmlType('number')
          ->addRule(Form::NUMERIC)
          ->setRequired('Musíte vědět kolik bot velikosti 45 máme!');//tady by mohly být další kontroly pro min, max atp.


      $this->addCheckbox('available', 'Zobrazovat v eshopu')
      ->setDefaultValue(true);

    #region obrázek
      $photoUpload=$this->addUpload('photo','Fotka produktu');
      //pokud není zadané ID produktu, je nahrání fotky povinné
      $photoUpload //vyžadování nahrání souboru, pokud není známé productId
      ->addConditionOn($productId, Form::EQUAL, '')
          ->setRequired('Pro uložení nového produktu je nutné nahrát jeho fotku.');

      $photoUpload //limit pro velikost nahrávaného souboru
      ->addRule(Form::MAX_FILE_SIZE, 'Nahraný soubor je příliš velký', 1000000);

      $photoUpload //kontrola typu nahraného souboru, pokud je nahraný
      ->addCondition(Form::FILLED)
          ->addRule(function(Nette\Forms\Controls\UploadControl $photoUpload){
              $uploadedFile = $photoUpload->value;
              if ($uploadedFile instanceof Nette\Http\FileUpload){
                  $extension=strtolower($uploadedFile->getImageFileExtension());
                  return in_array($extension,['jpg','jpeg','png']);
              }
              return false;
          },'Je nutné nahrát obrázek ve formátu JPEG či PNG.');

    #endregion obrázek

    $this->addSubmit('ok','uložit')
      ->onClick[]=function(SubmitButton $button){
        $values=$this->getValues('array');
        if (!empty($values['productId'])){
          try{
            $product=$this->productsFacade->getProduct($values['productId']);
          }catch (\Exception $e){
            $this->onFailed('Požadovaný produkt nebyl nalezen.');
            return;
          }
        }else{
          $product=new Product();
        }
        $product->assign($values,['title','url','description','available','brand','color','cut','model']);
        $product->category=$this->categoriesFacade->getCategory($values['categoryId']);
        $product->price=floatval($values['price']);
        $product->thirtyeight=intval($values['thirtyeight']);
        $product->thirtynine=intval($values['thirtynine']);
        $product->forty=intval($values['forty']);
        $product->fortyone=intval($values['fortyone']);
        $product->fortytwo=intval($values['fortytwo']);
        $product->fortythree=intval($values['fortythree']);
        $product->fortyfour=intval($values['fortyfour']);
        $product->fortyfive=intval($values['fortyfive']);
        $this->productsFacade->saveProduct($product);
        $this->setValues(['productId'=>$product->productId]);

        //uložení fotky
        if (($values['photo'] instanceof Nette\Http\FileUpload) && ($values['photo']->isOk())){
          try{
            $this->productsFacade->saveProductPhoto($values['photo'], $product);
          }catch (\Exception $e){
            $this->onFailed('Produkt byl uložen, ale nepodařilo se uložit jeho fotku.');
          }
        }

        $this->onFinished('Produkt byl uložen.');
      };
    $this->addSubmit('storno','zrušit')
      ->setValidationScope([$productId])
      ->onClick[]=function(SubmitButton $button){
        $this->onCancel();
      };
  }

  /**
   * Metoda pro nastavení výchozích hodnot formuláře
   * @param Product|array|object $values
   * @param bool $erase = false
   * @return $this
   */
  public function setDefaults($values, bool $erase = false):self {
    if ($values instanceof Product){
      $values = [
        'productId'=>$values->productId,
        'categoryId'=>$values->category?$values->category->categoryId:null,
        'title'=>$values->title,
        'url'=>$values->url,
        'description'=>$values->description,
        'price'=>$values->price,
          'brand'=>$values->brand,
          'color'=>$values->color,
          'cut'=>$values->cut,
          'model'=>$values->model,
          'thirtyeight'=>$values->thirtyeight,
          'thirtynine'=>$values->thirtynine,
          'forty'=>$values->forty,
          'fortyone'=>$values->fortyone,
          'fortytwo'=>$values->fortytwo,
          'fortythree'=>$values->fortythree,
          'fortyfour'=>$values->fortyfour,
          'fortyfive'=>$values->fortyfive
      ];
    }
    parent::setDefaults($values, $erase);
    return $this;
  }

}