<?php declare(strict_types=1);

namespace App\AdminModule\Presenters;

use App\AdminModule\Components\UserEditForm\UserEditForm;
use App\AdminModule\Components\UserEditForm\UserEditFormFactory;
use App\Model\Entities\User;
use App\Model\Facades\UsersFacade;
use Nette\DI\Attributes\Inject;

class UsersPresenter extends BasePresenter {

	#[Inject]
	public UsersFacade $usersFacade;

	#[Inject]
	public UserEditFormFactory $userEditFormFactory;

	private User $userToEdit;

	public function renderDefault():void {
		$this->template->users = $this->usersFacade->findUsers();
	}

	public function actionEdit(?int $id):void {
		$this->template->id = $id;

		if ($id === null) {
			return;
		}

		try {
			$this->userToEdit = $this->usersFacade->getUser($id);
			$this->template->name = $this->userToEdit->name;
		} catch (\Throwable) {
			$this->flashMessage('User not found', 'warning');
			$this->redirect('default');
		}
	}

	protected function createComponentUserEditForm(): UserEditForm
	{
		return $this->userEditFormFactory->create($this->userToEdit);
	}
}
