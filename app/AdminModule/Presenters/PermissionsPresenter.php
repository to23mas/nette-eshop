<?php declare(strict_types=1);

namespace App\AdminModule\Presenters;

use App\AdminModule\Components\PermissionEditForm\PermissionEditForm;
use App\AdminModule\Components\PermissionEditForm\PermissionEditFormFactory;
use App\AdminModule\Components\PermissionGrid\PermissionGrid;
use App\AdminModule\Components\PermissionGrid\PermissionGridFactory;
use App\Model\Entities\Permission;
use App\Model\Entities\Role;
use App\Model\Facades\PermissionsFacade;
use App\Model\Facades\RoleFacade;
use Nette\DI\Attributes\Inject;

class PermissionsPresenter extends BasePresenter {

	#[Inject]
	public RoleFacade $roleFacade;

	#[Inject]
	public PermissionsFacade $permissionsFacade;

	#[Inject]
	public PermissionGridFactory $permissionGrid;

	#[Inject]
	public PermissionEditFormFactory $permissionsEditForm;

	private ?Role $role = null;

	private ?Permission $permission = null;

	public function renderDefault(): void {
		$this->template->roles = $this->roleFacade->find();
	}

	public function actionPermissions(?string $roleId): void {
		$this->template->roleId = $roleId;

		if ($roleId === null) {
			return;
		}

		try {
			$this->role = $this->roleFacade->getRole($roleId);
		} catch (\Throwable $e) {
			$this->flashMessage('Role nenalezena', 'warning');
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
		} catch (\Throwable) {}
		$this->redirect('default');
	}

	public function actionEdit(?string $id): void
	{
		if ($id !== null) {
			$this->permission = $this->permissionsFacade->get((int) $id);
			$this->template->roleId = $this->permission->roleId;
		}
	}

	protected function createComponentPermissionGrid(): PermissionGrid
	{
		return $this->permissionGrid->create($this->role);
	}

	protected function createComponentPermissionEditForm(): PermissionEditForm
	{
		return $this->permissionsEditForm->create($this->permission);
	}
}

