<?php declare(strict_types=1);

namespace App\AdminModule\Presenters;

use App\AdminModule\Components\UserEditForm\UserEditForm;
use App\AdminModule\Components\UserEditForm\UserEditFormFactory;
use App\Model\Entities\User;
use App\Model\Facades\RoleFacade;
use App\Model\Facades\UsersFacade;
use Nette\DI\Attributes\Inject;
use Nette\Utils\Paginator;

class UsersPresenter extends BasePresenter {

	#[Inject]
	public UsersFacade $usersFacade;

	#[Inject]
	public UserEditFormFactory $userEditFormFactory;

	#[Inject]
	public RoleFacade $roleFacade;

	private User $userToEdit;

	public function renderDefault(?string $selectedRole = null, int $page = 1): void {
		$paginator = new Paginator;
		$paginator->setItemsPerPage(10);
		$paginator->setPage($page);

		$this->template->selectedRole = $selectedRole;

		if ($selectedRole !== null) {
			$paginator->setItemCount($this->usersFacade->getCount(['role_id' => $selectedRole]));
			$this->template->users = $this->usersFacade->findAllBy(
				['role_id' => $selectedRole], $paginator->getOffset(), $paginator->getLength()
			);
		} else {
			$paginator->setItemCount($this->usersFacade->getCount());
			$this->template->users= $this->usersFacade->findAllBy(
				null, $paginator->getOffset(), $paginator->getLength()
			);
		}

		$this->template->paginator = $paginator;
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
