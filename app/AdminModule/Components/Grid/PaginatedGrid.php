<?php declare(strict_types=1);

namespace App\AdminModule\Components\Grid;

use Nette\Application\UI\Control;
use Nette\Utils\Paginator;

class PaginatedGrid extends Control {

	public Paginator $paginator;

	public function __construct(int $limit = 8, int $count)
	{
		$this->paginator = new Paginator;
		$this->paginator->setItemsPerPage($limit);
		$this->paginator->setItemCount($count);
		$this->paginator->setPage(1);
	}

	protected function setLimit(int $limit): void
	{
		$this->limit = $limit;
	}

	public function handleChange(int $page) {
		$this->paginator->setPage($page);
	}
}
