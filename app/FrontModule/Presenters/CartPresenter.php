<?php

namespace App\FrontModule\Presenters;

use App\Model\Entities\Objednavka;
use App\Model\Facades\CartFacade;
use App\Model\Facades\ObjednavkaFacade;
use App\Model\Facades\ObjednavkaIdFacade;
use App\Model\Facades\UsersFacade;
use App\Model\Repositories\CartItemRepository;
use App\Model\Repositories\ObjednavkaIdRepository;
use Nette\Application\UI\Form;
use Nette\Utils\Random;

class CartPresenter extends BasePresenter
{
    /** @var CartFacade $certFacade*/
    private $cartFacade;
    /** @var UsersFacade $usersFacade*/
    private $usersFacade;
    /** @var ObjednavkaFacade $objednavkaFacade*/
    private $objednavkaFacade;
    /** @var CartItemRepository $cartItemRepository*/
    private $cartItemRepository;


    /** @persistent */
    public $random ;

    public function renderList() {

        $this->template->cart = $this->cartFacade->getCartByUser($this->usersFacade->getUser($this->user->id));
    }

    public function renderNahled() {

        $this->template->cart = $this->cartFacade->getCartByUser($this->usersFacade->getUser($this->user->id));
    }

    protected function createComponentObjednavkaForm(): Form
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
        $form->addText('jmeno', 'Jméno a Přijmení')
            ->setRequired()->setAttribute('class','form-control mr-3');

        $form->addEmail('email', 'Zadejte Email')
            ->setRequired()->setAttribute('class','form-control mr-3');
        $form->addTextArea('zprava', 'Poznamka k objednavce')
            ->setRequired()->setAttribute('class','form-control mr-3');
        $form->addHidden('objednavkaId',random_int(0,2147483647));


        $form->addSubmit('send', 'Odeslat objednavku')->setAttribute('class','btn btn-primary');


        $form->onSuccess[] = [$this, 'commentFormSucceeded'];

        return $form;
    }
    //Metoda pro uložení komentáře
    public function commentFormSucceeded(\stdClass $values/*,string $url*/): void
    {

        //$objednavkaId = $this->objednavkaIdFacade->getId(1);
        $objednavka = new Objednavka();
        $objednavka->jmeno = $values->jmeno;
        $objednavka->email = $values->email;
        $objednavka->objednavkaId=(int)$values->objednavkaId;
        $objednavka->zprava =$values->zprava;
        $objednavka->stav = "přijato";
        $objednavka->user = $this->usersFacade->getUser($this->user->id);
        $objednavka->cena = (int)$this->cartFacade->getCartByUser($this->usersFacade->getUser($this->user->id))->getTotalPrice();
        $cart=$this->cartFacade->getCartByUser($this->usersFacade->getUser($this->user->id));
        $this->objednavkaFacade->saveObjednavka($objednavka);
        foreach ($cart->items as $item){
            $cartItem = $item;
            $cartItem->objednavka = $this->objednavkaFacade->getObjednavka((int)$values->objednavkaId);
            $cartItem->cart = null;
            $this->cartFacade->saveCartItemId($cartItem);
            $this->cartFacade->saveCartItemId($item);
        }
        //$this->objednavkaFacade->saveObjednavka($objednavka);
        $this->flashMessage('Objednávka byla odeslána, děkujeme!', 'info');
        $this->redirect('Product:list');

    }





    public function injectCartFacade(CartFacade $cartFacade){
        $this->cartFacade = $cartFacade;
    }
    public function injectUsersFacade(UsersFacade $usersFacade){
        $this->usersFacade = $usersFacade;
    }

    public  function injectObjednavkaFacade(ObjednavkaFacade $objednavkaFacade){
        $this->objednavkaFacade = $objednavkaFacade;
    }
    public function injectCartItemRepository(CartItemRepository $cartItemRepository){
        $this->cartItemRepository = $cartItemRepository;

    }
}