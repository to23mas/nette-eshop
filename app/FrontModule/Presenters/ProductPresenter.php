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




  public $category_filter;
    /** @persistent */

    public $productId;
    /** @persistent */

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
  }

  /**
   * Akce pro vykreslení přehledu produktů
   */
  public function renderList(?form $form):void {
    //TODO tady by mělo přibýt filtrování podle kategorie, stránkování atp.
      if ($this->category_filter == null) {
          $this->template->products = $this->productsFacade->findProducts(['order' => 'title']);
      }
      else{

          $categories = $this->categoriesFacade->findCategories();
          $filterArray = array();
          $true_flag = false;
          foreach ($categories as $category){
              if ($this->category_filter[$category->categoryId]){
                array_push($filterArray,$category->categoryId);
                $true_flag = true;
              }
          }
          if ($true_flag) {
              $this->template->products = $this->productsFacade->getProductsByFilter($filterArray);
          }else{
              $this->template->products = $this->productsFacade->findProducts(['order' => 'title']);
          }
      }


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

        $this->category_filter = $form->getValues();

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
    $comment->product = $this->productsFacade->getProduct('1');
    $comment->name = $this->user->identity->name;
    $comment->content = $values->content;
    //$comment->created = date("Y.m.d H:i");
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


  #endregion injections
}