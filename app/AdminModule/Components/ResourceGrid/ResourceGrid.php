<?php declare(strict_types=1);

namespace App\AdminModule\Components\ResourceGrid;

use App\AdminModule\Components\Grid\PaginatedGrid;
use App\Model\Entities\Role;
use App\Model\Facades\PermissionsFacade;
use App\Model\Facades\ResourcesFacade;
use App\Model\Repositories\PermissionRepository;
use App\Model\Repositories\RoleRepository;
use Nette\Application\UI\Control;
use Nette\Utils\Paginator;


final class ResourceGrid extends PaginatedGrid {

	public function __construct(
		private readonly ResourcesFacade $resourcesFacade,
		private int $page = 1,
	) {
		parent::__construct(8, $this->resourcesFacade->getCount());
	}

	public function render(): void
	{
		$this->getTemplate()->resources = $this->resourcesFacade->findAllBy(null, $this->paginator->getOffset(), $this->paginator->getLength());
		$this->getTemplate()->paginator = $this->paginator;
		$this->getTemplate()->setFile(__DIR__ . '/templates/grid.latte');
		$this->getTemplate()->render();
	}

	public function handleEdit(string $resourceId): void {
		$this->presenter->redirect(
			'Resources:edit', ['resourceId' => $resourceId]
		);
	}

	public function handleDelete(string $resourceId): void {
		try {
			$this->resourcesFacade->delete($resourceId);
			$this->presenter->flashMessage('Resource úspěšně smazán', 'info');
		} catch (\Throwable) {
			$this->presenter->flashMessage('Nepodařilo se smazat resource', 'danger');
		}

		$this->presenter->redirect('Resources:default');
	}
}

