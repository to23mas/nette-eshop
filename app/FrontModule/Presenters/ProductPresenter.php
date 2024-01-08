<?php

namespace App\FrontModule\Presenters;

use App\Bootstrap;
use App\FrontModule\Components\ProductCartForm\ProductCartForm;
use App\FrontModule\Components\ProductCartForm\ProductCartFormFactory;
use App\Model\Entities\Comments;
use App\Model\Entities\LikedBy;
use App\Model\Facades\CommentsFacade;
use App\Model\Facades\ProductsFacade;
use App\Model\Facades\LikedByFacade;
use App\Model\Facades\CategoriesFacade;
use App\Model\Facades\SizeFacade;
use App\Model\Facades\UsersFacade;
use mysql_xdevapi\Exception;
use Nette;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Form;
use Nette\Application\UI\Multiplier;
use Nette\Utils\Paginator;

/**
 * Class ProductPresenter
 * @package App\FrontModule\Presenters
 */
class ProductPresenter extends BasePresenter{
  private ProductsFacade $productsFacade;
  private ProductCartFormFactory $productCartFormFactory;

  private CategoriesFacade $categoriesFacade;

  private CommentsFacade $commentsFacade;

  private SizeFacade $sizeFacade;




  public $filterArray;
    /** @persistent */

    public $productId;
    /** @persistent */
    /** @persistent */
    public $page = 1;

  /**
   * Akce pro zobrazení jednoho produktu
   * @param string $url
   * @throws BadRequestException
   */
  public function renderShow(string $url):void {
    try{
      $product = $this->productsFacade->getProductByUrl($url);
    }catch (\Exception $e){
      throw new BadRequestException('Produkt nebyl nalezen.');
    }

      $this->template->product = $product;
      $this->productId= $product->productId;
      $this->template->logged = $this->user->loggedIn;
      var_dump($product->size);

  }

  /**
   * Akce pro vykreslení přehledu produktů
   */
  public function renderList(?form $form):void {
    //TODO tady by mělo přibýt filtrování podle kategorie, stránkování atp.

      var_dump($this->filterArray);
      if ($this->filterArray == null){
          $this->template->products = $this->productsFacade->findProducts(['order' => 'title']);
      }elseif (count($this->filterArray) == 0) {
          $this->template->products = $this->productsFacade->findProducts(['order' => 'title']);
      }
      else{

          $this->template->products = $this->productsFacade->getProductsByFilter($this->filterArray);

      }

      //Paginator
      $paginator = new Paginator();
      if($this->filterArray ==null) {
      }elseif (count($this->filterArray) == 0){

            $paginator->setItemCount($this->productsFacade->findProductsCount());}
      else {
          $paginator->setItemCount(count($this->productsFacade->getProductsByFilter($this->filterArray)));
      }
      $paginator->setItemsPerPage(4);

      $currentPage = min($this->page,$paginator->pageCount);
      $currentPage = max($currentPage,1);
      if($currentPage !=$this->page){
          $this->redirect('list',['page'=>$currentPage]);

      }
      $paginator->setPage($this->page);
      $this->template->paginator = $paginator;


  }

    protected function createComponentFilterForm(): Form
    {
        $categories = $this->categoriesFacade->findCategories();


        $form = new Form;
        $form->getElementPrototype()->class('form-group');
        $renderer = $form->getRenderer();
        $renderer->wrappers['controls']['container'] = NULL;
        $renderer->wrappers['pair']['container'] = 'div class=form-group';
        $renderer->wrappers['pair']['.error'] = 'has-error';
        $renderer->wrappers['control']['container'] = 'div class=col-sm-9';
        $renderer->wrappers['label']['container'] = 'div class="col-sm-3 control-label"';
        $renderer->wrappers['control']['description'] = 'span class=help-block';
        $renderer->wrappers['control']['errorcontainer'] = 'span class=help-block';

        $i = 1;
        foreach ($categories as $category){
            $title = $category->title;
            $id = $category->categoryId;
            $form->addCheckbox("$id", "$title",);

            $i = $i+1;
        }
        $form->addSubmit('send', 'Filtrovat       ')->setAttribute('class','btn btn-primary');
        $form->onSuccess[] = [$this, 'formFilterSucceeded'];
        return $form;


    }

    public function formFilterSucceeded($form, $values): void
    {

        $category_filter = $form->getValues();
        $categories = $this->categoriesFacade->findCategories();

        $this->filterArray = [];
        foreach ($categories as $category){
            if ($category_filter[$category->categoryId]){
                array_push($this->filterArray,$category->categoryId);

            }
        }

    }

    protected function createComponentCommentForm(): Form
    {
        $form = new Form;
        $form->getElementPrototype()->class('form-group');
        $renderer = $form->getRenderer();
        $renderer->wrappers['controls']['container'] = NULL;
        $renderer->wrappers['pair']['container'] = 'div class=form-group';
        $renderer->wrappers['pair']['.error'] = 'has-error';
        $renderer->wrappers['control']['container'] = 'div class=col-sm-9';
        $renderer->wrappers['label']['container'] = 'div class="col-sm-3 control-label"';
        $renderer->wrappers['control']['description'] = 'span class=help-block';
        $renderer->wrappers['control']['errorcontainer'] = 'span class=help-block';
        $form->addTextArea('content', 'Komentář:')->setHtmlAttribute('placeholder','Zadejte komentář')
            ->setRequired()->setAttribute('class','form-control mr-3');

        $form->addSubmit('send', 'Odeslat komentář')->setAttribute('class','btn btn-primary');
        $form->addHidden('productId',$this->productId);
        $form->onSuccess[] = [$this,'formCommentSucceded'];

        return $form;
    }

public function formCommentSucceded($form,$values){
    $comment = new Comments();
    $comment->product = $this->productsFacade->getProduct($this->productId);
    $comment->name = $this->user->identity->name;
    $comment->content = $values->content;
    $comment->userId = $this->user->id;
    $this->commentsFacade->saveComment($comment);
    $this->flashMessage('Děkuji za komentář', 'success');
    $this->redirect('this');

}


  protected function createComponentProductCartForm():Multiplier {
    return new Multiplier(function($productId){
      $form = $this->productCartFormFactory->create();
      $form->setDefaults(['productId'=>$productId]);
      $form->onSubmit[]=function(ProductCartForm $form){
        try{
          $product = $this->productsFacade->getProduct($form->values->productId);
          //kontrola zakoupitelnosti
        }catch (\Exception $e){
          $this->flashMessage('Produkt nejde přidat do košíku','error');
          $this->redirect('this');
        }
        //přidání do košíku
        /** @var CartControl $cart */
        $cart = $this->getComponent('cart');
        $cart->addToCart($product, (int)$form->values->count);
        $this->redirect('this');
      };
      return $form;
    });
  }

  #region injections
  public function injectProductsFacade(ProductsFacade $productsFacade):void {
    $this->productsFacade=$productsFacade;
  }

  public function injectProductCartFormFactory(ProductCartFormFactory $productCartFormFactory):void {
    $this->productCartFormFactory=$productCartFormFactory;
  }

  public function injectCategoriesFacade(CategoriesFacade $categoriesFacade):void{
      $this->categoriesFacade = $categoriesFacade;

  }
  public function injectCommentsFacade(CommentsFacade $commentsFacade):void{
        $this->commentsFacade = $commentsFacade;
    }

  public function injectSizeFacade(SizeFacade $sizeFacade):void{
        $this->sizeFacade = $sizeFacade;
    }


  #endregion injections
}