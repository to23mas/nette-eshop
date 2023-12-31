<?php declare(strict_types=1);

namespace App\Model\Facades;

use App\Model\Entities\Role;
use App\Model\Repositories\RoleRepository;

final class RoleFacade {

	public function __construct(
		private readonly RoleRepository $roleRepository
	) {}

	/**
	 * @throws \Exception
	 */
	public function get(string $roleId): Role {
		return $this->roleRepository->findBy(['role_id' => $roleId]);
	}

	public function find(): array {
		return $this->roleRepository->findAll();
	}

}

