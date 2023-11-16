<?php

namespace App\AdminModule\Components\ObjednavkaEditForm;

use App\Model\Entities\Objednavka;
use App\Model\Entities\User;
use App\Model\Facades\ObjednavkaFacade;
use App\Model\Facades\UsersFacade;
use Nette;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\SmartObject;
use Nextras\FormsRendering\Renderers\Bs4FormRenderer;
use Nextras\FormsRendering\Renderers\FormLayout;

/**
 * Class ObjednavkaEditForm
 * @package App\AdminModule\Components\ObjednavkaEditForm
 *
 * @method onFinished(string $message = '')
 * @method onFailed(string $message = '')
 * @method onCancel()
 */
class ObjednavkaEditForm extends Form{

    use SmartObject;

    /** @var callable[] $onFinished */
    public $onFinished = [];
    /** @var callable[] $onFailed */
    public $onFailed = [];
    /** @var callable[] $onCancel */
    public $onCancel = [];
    /** @var UsersFacade $usersFacade */
    private $usersFacade;
    /** @var ObjednavkaFacade $objednavkaFacade */
    private $objednavkaFacade;

    /**
     * TagEditForm constructor.
     * @param Nette\ComponentModel\IContainer|null $parent
     * @param string|null $name
     * @param ObjednavkaFacade $objednavkaFacade
     * @noinspection PhpOptionalBeforeRequiredParametersInspection
     */
    public function __construct(Nette\ComponentModel\IContainer $parent = null, string $name = null, ObjednavkaFacade $objednavkaFacade){
        parent::__construct($parent, $name);
        $this->setRenderer(new Bs4FormRenderer(FormLayout::VERTICAL));
        $this->objednavkaFacade=$objednavkaFacade;
        $this->createSubcomponents();
    }

    private function createSubcomponents(){
        $objednavkaId=$this->addHidden('objednavkaId');
        $this->addHidden('jmeno');
        $this->addHidden('email');
        $this->addHidden('zprava');
        $this->addHidden('created');
        $this->addHidden('cena');
        $this->addText('stav','stav:')
            ->setRequired('Musíte zadat stav objednávky');

        $this->addSubmit('ok','uložit')
            ->onClick[]=function(SubmitButton $button){
            $values=$this->getValues('array');
            if (!empty($values['objednavkaId'])){
                try{
                    $objednavka=$this->objednavkaFacade->getObjednavka($values['objednavkaId']);
                }catch (\Exception $e){
                    $this->onFailed('Požadovaná objednávka nebyla nalezena.');
                    return;
                }
            }else{
                $objednavka=new Objednavka();
            }
            $objednavka->assign($values,['jmeno, email, zprava, stav']);
            $objednavka->cena=intval($values['cena']);
            $objednavka->user=$this->usersFacade->getUser($values['userId']);
            $this->objednavkaFacade->saveObjednavka($objednavka);
            $this->setValues(['objednavkaId'=>$objednavka->objednavkaId]);
            $this->onFinished('Změna objednávky byla uložena.');
        };
        $this->addSubmit('storno','zrušit')
            ->setValidationScope([$objednavkaId])
            ->onClick[]=function(SubmitButton $button){
            $this->onCancel();
        };
    }

    /**
     * Metoda pro nastavení výchozích hodnot formuláře
     * @param Objednavka|array|object $values
     * @param bool $erase = false
     * @return $this
     */
    public function setDefaults($values, bool $erase = false):self {
        if ($values instanceof Objednavka){
            $values = [
                'objednavkaId'=>$values->objednavkaId,
                'jmeno'=>$values->jmeno,
                'email'=>$values->email,
                'zprava'=>$values->zprava,
                'userId'=>$values->user->userId,
                'cena'=>$values->cena,
                'stav'=>$values->stav
            ];
        }
        parent::setDefaults($values, $erase);
        return $this;
    }

}