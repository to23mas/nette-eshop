<?php declare(strict_types=1);

namespace App\Model\Repositories;

use App\Model\Entities\Category;
use Dibi\Result;

class CategoryRepository extends BaseRepository
{

	public function create(string $title, string $desc): void
	{
		$this->connection->nativeQuery(sprintf(
			"INSERT INTO `category` (`title`, `description`) VALUES ('%s', '%s');",
			$title, $desc,
		));
	}

	public function getIdByTitle(string $title): Result
	{
		return $this->connection->nativeQuery(sprintf(
			"SELECT `category_id` FROM `category` WHERE `title` = '%s'",
			$title,
		));
	}

	public function deleteById(int $categoryId): void
	{
		$this->connection->nativeQuery(sprintf(
			"DELETE FROM `category` WHERE `category_id` = ('%s')",
			$categoryId,
		));
	}
}
