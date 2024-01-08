<?php declare(strict_types=1);

namespace App\AdminModule\Presenters;

use App\AdminModule\Components\PermissionEditForm\PermissionEditForm;
use App\AdminModule\Components\PermissionEditForm\PermissionEditFormFactory;
use App\AdminModule\Components\PermissionGrid\PermissionGrid;
use App\AdminModule\Components\PermissionGrid\PermissionGridFactory;
use App\AdminModule\Components\ResourceEditForm\ResourceEditForm;
use App\AdminModule\Components\ResourceEditForm\ResourceEditFormFactory;
use App\Model\Entities\Permission;
use App\Model\Entities\Resource;
use App\Model\Entities\Role;
use App\Model\Facades\PermissionsFacade;
use App\Model\Facades\ResourcesFacade;
use App\Model\Facades\RoleFacade;
use Nette\DI\Attributes\Inject;

class PermissionsPresenter extends BasePresenter {

	#[Inject]
	public RoleFacade $roleFacade;

	#[Inject]
	public PermissionsFacade $permissionsFacade;

	#[Inject]
	public ResourcesFacade $resourcesFacade;

	#[Inject]
	public PermissionGridFactory $permissionGrid;

	#[Inject]
	public ResourceEditFormFactory $resourceEditForm;

	#[Inject]
	public PermissionEditFormFactory $permissionsEditForm;

	private ?Role $role = null;

	private ?Permission $permission = null;

	private ?Resource $resource = null;

	public function renderDefault(?string $roleId): void
	{
		if ($roleId !== null) {
			$this->role = $this->roleFacade->get($roleId);
		}
	}

	public function actionEdit(?string $permissionId = null, ?string $roleId = null): void
	{
		if ($permissionId !== null) {
			$this->permission = $this->permissionsFacade->get((int) $permissionId);
			$this->template->roleId = $this->permission->roleId;
			$this->template->permission = $this->permission->permissionId;
		}

		if ($roleId !== null) {
			$this->role = $this->roleFacade->get($roleId);
			$this->template->roleId = $roleId;
		}
	}

	public function actionEditResource(?string $resourceId  = null, ?string $selectedRole = null): void
	{
		if ($resourceId !== null) {
			$this->resource = $this->resourcesFacade->get($resourceId);
		}

		if ($selectedRole !== null) {
			$this->template->selectedRole = $selectedRole;
		}
	}

	protected function createComponentPermissionGrid(): PermissionGrid
	{
		return $this->permissionGrid->create($this->role);
	}

	protected function createComponentResourceEditForm(): ResourceEditForm
	{
		return $this->resourceEditForm->create($this->resource, false);
	}

	protected function createComponentPermissionEditForm(): PermissionEditForm
	{
		return $this->permissionsEditForm->create($this->permission, $this->role);
	}
}

