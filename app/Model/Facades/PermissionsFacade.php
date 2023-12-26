<?php declare(strict_types=1);

namespace App\Model\Facades;

use App\Model\Entities\Permission;
use App\Model\Repositories\PermissionRepository;

final class PermissionsFacade {

	public function __construct(
		private readonly PermissionRepository $permissionRepository
	) {}

	/**
	 * @throws \Exception
	 */
	public function get(int $permissionId): Permission {
		return $this->permissionRepository->findBy(['permission_id' => $permissionId]);
	}

	public function find(): array {
		return $this->permissionRepository->findAll();
	}

	public function findBy(array $arg): array {
		return $this->permissionRepository->findAllBy($arg);
	}

	public function save(Permission &$permission): bool {
		return (bool) $this->permissionRepository->persist($permission);
	}

	public function deleteEntity(Permission &$permission): void {
		$this->permissionRepository->delete($permission);
	}
}


