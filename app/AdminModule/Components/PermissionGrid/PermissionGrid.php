<?php declare(strict_types=1);

namespace App\AdminModule\Components\PermissionGrid;

use App\Model\Entities\Role;
use App\Model\Repositories\PermissionRepository;
use Nette\Application\UI\Control;


final class PermissionGrid extends Control {

	public function __construct(
		private ?Role $role,
		private readonly PermissionRepository $permissionsRepository,
	) {}

	public function render(): void
	{
		$this->getTemplate()->permissions = $this->permissionsRepository->findAllBy(['role_id' => $this->role->roleId]);
		$this->getTemplate()->setFile(__DIR__ . '/templates/grid.latte');
		$this->getTemplate()->render();
	}

	public function handleEdit(string $id): void {
		$this->presenter->redirect('Permissions:edit', ['id' => $id]);
	}
}


