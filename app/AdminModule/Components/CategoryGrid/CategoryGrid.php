<?php declare(strict_types=1);

namespace App\AdminModule\Components\CategoryGrid;

use App\Model\Entities\Role;
use App\Model\Facades\CategoriesFacade;
use App\Model\Facades\PermissionsFacade;
use App\Model\Repositories\PermissionRepository;
use App\Model\Repositories\RoleRepository;
use Nette\Application\UI\Control;
use Nette\Utils\Paginator;


final class	CategoryGrid extends Control {

	public function __construct(
		private readonly CategoriesFacade $categoriesFacade,
	) { }

	public function render(): void {

		$this->getTemplate()->categories = $this->categoriesFacade->findCategories();
		$this->getTemplate()->setFile(__DIR__ . '/templates/grid.latte');
		$this->getTemplate()->render();
	}

	public function handleEdit(int $categoryId): void
	{
		$this->presenter->redirect('Category:edit', ['categoryId' => $categoryId]);
	}

	public function handleDelete(int $categoryId): void
	{
		try {
			$this->categoriesFacade->deleteById($categoryId);
			$this->presenter->flashMessage('Kategorie úspěšně smazána', 'info');
		} catch (\Throwable $e) {
			$this->presenter->flashMessage('Nepodařilo se smazat kategorii', 'danger');
		}

		$this->presenter->redirect('Category:default');
	}
}

