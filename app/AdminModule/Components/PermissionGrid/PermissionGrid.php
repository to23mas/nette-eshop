<?php declare(strict_types=1);

namespace App\AdminModule\Components\PermissionGrid;

use App\Model\Entities\Role;
use App\Model\Facades\PermissionsFacade;
use App\Model\Repositories\PermissionRepository;
use App\Model\Repositories\RoleRepository;
use Nette\Application\UI\Control;
use Nette\Utils\Paginator;


final class PermissionGrid extends Control {

	public function __construct(
		private ?Role $role,
		private readonly PermissionRepository $permissionsRepository,
		private readonly PermissionsFacade $permissionsFacade,
		private readonly RoleRepository $roleRepository,
	) {}

	public function render(): void {
		$paginator = new Paginator;
		$paginator->setPage(1);
		$paginator->setItemsPerPage(15);
		$
		$this->getTemplate()->roles = $this->roleRepository->findAll();

		if ($this->role === null) {
			$this->getTemplate()->permissions = $this->permissionsFacade->find();
		} else {
			$this->getTemplate()->selectedRole = $this->role->roleId;
			$this->getTemplate()->permissions = $this->permissionsFacade->findBy(['role_id' => $this->role->roleId]);
		}
		$this->getTemplate()->setFile(__DIR__ . '/templates/permissions.latte');
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
}


