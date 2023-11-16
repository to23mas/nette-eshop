<?php

namespace App\FrontModule\Presenters;

use App\Model\Facades\ObjednavkaFacade;
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
use Nette\Application\BadRequestException;
use Nette\Application\UI\Form;
use Nette\Application\UI\Multiplier;
use Nette\Utils\Paginator;

class ObjednavkaPresenter extends BasePresenter
{
    /**@var ObjednavkaFacade $objednavkaFacade */
    private $objednavkaFacade;
    public function renderList():void {
        $this->template->objednavka = $this->objednavkaFacade->findObjednavkas(['order'=>'created DESC']);

    }

    public function renderDetail(int $objednavkaId) {

        $this->template->objednavka = $this->objednavkaFacade->findObjednavka($objednavkaId);
    }


    public function injectObjednavkaFacade(ObjednavkaFacade $objednavkaFacade){
        $this->objednavkaFacade = $objednavkaFacade;
    }


}