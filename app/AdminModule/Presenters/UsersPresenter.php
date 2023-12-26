<?php declare(strict_types=1);

namespace App\AdminModule\Presenters;

use App\AdminModule\Components\UserEditForm\UserEditForm;
use App\AdminModule\Components\UserEditForm\UserEditFormFactory;
use App\Model\Entities\User;
use App\Model\Facades\RoleFacade;
use App\Model\Facades\UsersFacade;
use Nette\DI\Attributes\Inject;

class UsersPresenter extends BasePresenter {

	#[Inject]
	public UsersFacade $usersFacade;

	#[Inject]
	public UserEditFormFactory $userEditFormFactory;

	#[Inject]
	public RoleFacade $roleFacade;

	private User $userToEdit;

	public function renderDefault(?string $selectedRole = null): void {
		$this->template->selectedRole = $selectedRole;

		if ($selectedRole !== null) {
			$this->template->users = $this->usersFacade->findBy(['role_id' => $selectedRole]);
		} else {
			$this->template->users = $this->usersFacade->findUsers();
		}

		$this->template->roles = $this->roleFacade->find();
	}

	public function actionEdit(?int $id): void {
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

	public function actionDelete(?int $id): void {
		if ($id === null) {
			return;
		}

		try {
			$user = $this->usersFacade->getUser($id);
			if ($user->role->roleId === 'admin') {
				$this->flashMessage('Uživatele s rolí "admin" nelze smazat', 'danger');
				$this->redirect('default');
			}
		} catch (\Throwable) {
			$this->flashMessage('Uživatel nenalezen', 'warning');
			$this->redirect('default');
		}

		try {
			$this->usersFacade->deleteUser($id);
			$this->flashMessage('Uživatel smazán', 'info');
		} catch (\Throwable $e) {}
		$this->redirect('default');
	}

	protected function createComponentUserEditForm(): UserEditForm
	{
		return $this->userEditFormFactory->create($this->userToEdit);
	}
}
