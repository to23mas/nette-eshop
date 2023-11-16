<?php

namespace App\FrontModule\Presenters;

use App\Model\Facades\CommentsFacade;

/**
 * Class CommentsPresenter
 * @package App\FrontModule\Presenters
 */
class CommentsPresenter extends BasePresenter{
    /** @var CommentsFacade $commentsFacade */
    private $commentsFacade;

    /**
     * Akce pro vykreslení seznamu komentářů
     */
    public function renderDefault():void {
        $this->template->comments=$this->commentsFacade->findComments();
    }

    /**
     * Akce pro smazání komentáře
     * @param int $id
     * @throws \Nette\Application\AbortException
     */
    public function actionDelete(int $id):void {
        try{
            $comments=$this->commentsFacade->getComments($id);
        }catch (\Exception $e){
            $this->flashMessage('Požadovaný komentář nebyl nalezen.', 'error');
            $this->redirect('default');
        }

        if (!$this->user->isAllowed($comments,'delete')){
            $this->flashMessage('Tento komentář není možné smazat.', 'error');
            $this->redirect('default');
        }

        if ($this->commentsFacade->deleteComment($comments)){
            $this->flashMessage('Komentář byl smazán.', 'info');
        }else{
            $this->flashMessage('Tento komentář není možné smazat.', 'error');
        }

        $this->redirect('default');
    }


    #region injections
    public function injectCommentsFacade(CommentsFacade $commentsFacade){
        $this->commentsFacade=$commentsFacade;
    }
    #endregion injections

}
