<?php declare(strict_types=1);

namespace App\AdminModule\Components\PermissionGrid;

use App\AdminModule\Components\Grid\PaginatedGrid;
use App\Model\Entities\Role;
use App\Model\Facades\PermissionsFacade;
use App\Model\Facades\RoleFacade;
use App\Model\Repositories\PermissionRepository;
use App\Model\Repositories\RoleRepository;
use Nette\Application\UI\Control;
use Nette\Utils\Paginator;


final class PermissionGrid extends PaginatedGrid {

	public function __construct(
		private ?Role $role,
		private readonly PermissionRepository $permissionsRepository,
		private readonly PermissionsFacade $permissionsFacade,
		private readonly RoleRepository $roleRepository,
		private readonly RoleFacade $roleFacade,
	) {
		parent::__construct(10,
			$role === null
				? $this->permissionsFacade->getCount()
				: $this->permissionsFacade->getCount(['role_id' => $this->role->roleId]));
	}

	public function render(): void {

		$this->getTemplate()->roles = $this->roleRepository->findAll();
		$this->getTemplate()->paginator = $this->paginator;

		if ($this->role === null) {
			$this->getTemplate()->permissions = $this->permissionsFacade->findAllBy( null, $this->paginator->getOffset(), $this->paginator->getLength() );
		} else {
			$this->getTemplate()->selectedRole = $this->role->roleId;
			$this->paginator->setItemCount($this->permissionsFacade->getCount(['role_id' => $this->role->roleId]));
			$this->getTemplate()->permissions = $this->permissionsFacade->findAllBy(
				['role_id' => $this->role->roleId],  $this->paginator->getOffset(), $this->paginator->getLength()
			);
		}
		$this->getTemplate()->setFile(__DIR__ . '/templates/permissions.latte');

		bdump($this->role);

		$this->getTemplate()->render();
	}

	public function handleEdit(string $permissionId): void {
		$this->presenter->redirect('Permissions:edit', [
			'permissionId' => $permissionId,
		]);
	}

	public function handleSelectRoles(?string $roleId): void {
		$this->presenter->redirect('Permissions:default', [
			'roleId' => $roleId,
		]);
	}

	public function handleDelete(int $permissionId, ?string $selectedRole): void {
		try {
			$this->permissionsFacade->deleteEntity($this->permissionsFacade->get($permissionId));
			$this->presenter->flashMessage('Smazání proběhlo úspěšně');
		} catch (\Throwable) {
			$this->presenter->flashMessage('Nepodařilo se smazat permision', 'danger');
		}

		$this->presenter->redirect('Permissions:default', [
			'roleId' => $selectedRole
		]);
	}

	public function handleAdd(?string $selectedRole): void {
		$this->presenter->redirect('Permissions:edit', [
			'permissionId' => null,
			'roleId' => $selectedRole,
		]);
	}

	public function handleEditResource(string $resourceId, ?string $selectedRole): void {
		$this->presenter->redirect('Permissions:editResource', [
			'resourceId' => $resourceId,
			'roleId' => $selectedRole,
		]);
	}

	public function handleChangeWithRole(int $page, ?string $selectedRole) {
		$this->paginator->setPage($page);
		if ($selectedRole !== null) {
			$this->role = $this->roleFacade->get($selectedRole);
		}
	}
}


