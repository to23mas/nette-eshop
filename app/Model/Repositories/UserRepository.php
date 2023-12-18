<?php declare(strict_types=1);

namespace App\Model\Repositories;

final class UserRepository extends BaseRepository {

	/**
	* @throws \Exception
	*/
	public function deleteUser(int $userId): void {
		// tenhle sprintf se mi nelíbí, ale bez něj to prostě nefachčí
		$this->connection->nativeQuery(sprintf('DELETE FROM `user` WHERE user_id = %s', $userId));
	}
}
