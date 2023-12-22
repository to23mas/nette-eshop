<?php declare(strict_types=1);

namespace App\Model\Facades;

use App\Model\Entities\Resource;
use App\Model\Repositories\ResourceRepository;

final class ResourcesFacade {

	public function __construct(
		private readonly ResourceRepository $resourceRepository,
	) {}

	/**
	 * @throws \Exception
	 */
	public function get(string $resourceId): Resource {
		return $this->resourceRepository->findBy(['resource_id' => $resourceId]);
	}

	public function find(): array {
		return $this->resourceRepository->findAll();
	}

	public function save(Resource &$resource): bool {
		return (bool) $this->resourceRepository->persist($resource);
	}

	public function update(string $oldId, string $newId): void {
		$this->resourceRepository->update($oldId, $newId);
	}

	public function create(string $id): void {
		$this->resourceRepository->create($id);
	}

	public function delete(string $id): void {
		$this->resourceRepository->delete($id);
	}
}
