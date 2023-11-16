<?php

namespace App\AdminModule\Presenters;

use App\Model\Facades\UsersFacade;
/**
 * Class CommentPresenter
 * @package App\AdminModule\Presenters
 */
class UsersPresenter extends BasePresenter{
    /** @var UsersFacade $usersFacade */
    private $usersFacade;

    /**
     * Akce pro vykreslení seznamu uživatelů
     */
    public function renderDefault():void {
        $this->template->users=$this->usersFacade->findUsers(['order'=>'name']);
    }

    /**
     * Akce pro smazání uživatele
     * @param int $id
     * @throws \Nette\Application\AbortException
     */
    public function actionDelete(int $id):void {
        try{
            $user=$this->usersFacade->getUser($id);
        }catch (\Exception $e){
            $this->flashMessage('Požadovaný uživatel nebyl nalezen.', 'error');
            $this->redirect('default');
        }

        if (!$this->user->isAllowed($user,'delete')){
            $this->flashMessage('Tohoto uživatel není možné smazat.', 'error');
            $this->redirect('default');
        }

        if ($this->usersFacade->deleteUser($user)){
            $this->flashMessage('Uživatel byl smazán.', 'info');
        }else{
            $this->flashMessage('Tohoto uživatel není možné smazat.', 'error');
        }

        $this->redirect('default');
    }

    #region injections
    public function injectUsersFacade(UsersFacade $usersFacade){
        $this->usersFacade=$usersFacade;
    }

    #endregion injections

}
