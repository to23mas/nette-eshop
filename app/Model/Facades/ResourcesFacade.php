<?php declare(strict_types=1);

namespace App\Model\Facades;

use App\Model\Entities\Resource;
use App\Model\Repositories\ResourceRepository;

final class ResourcesFacade {

	public function __construct(
		private readonly ResourceRepository $permissionRepository
	) {}

	/**
	 * @throws \Exception
	 */
	public function get(string $resourceId): Resource {
		return $this->permissionRepository->findBy(['resource_id' => $resourceId]);
	}

	public function find(): array {
		return $this->permissionRepository->findAll();
	}

}
