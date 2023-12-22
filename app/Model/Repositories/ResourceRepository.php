<?php

namespace App\Model\Repositories;

/**
 * Class ResourceRepository
 * @package App\Model\Repositories
 */
class ResourceRepository extends BaseRepository{

	// LeanMapper\Exception\InvalidArgumentException
	// ID can only be set in detached rows.
	public function update($oldId, $newId): void {
		$this->connection->nativeQuery(sprintf(
			"UPDATE `resource` SET `resource_id` = '%s' WHERE `resource`.`resource_id` = '%s'"
			,$newId, $oldId
		));
	}

	public function create($id): void {
		$this->connection->nativeQuery(sprintf(
			"INSERT INTO `resource` (`resource_id`) VALUES ('%s')"
			,$id
		));
	}

	public function delete($id): void {
		$this->connection->nativeQuery(sprintf(
			"DELETE FROM `resource` WHERE `resource_id` = ('%s')"
			,$id
		));
	}
}
