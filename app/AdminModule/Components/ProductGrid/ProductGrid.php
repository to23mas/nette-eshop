<?php declare(strict_types=1);

namespace App\AdminModule\Components\ProductGrid;

use App\Model\Facades\ProductsFacade;
use Nette\Application\UI\Control;

final class	ProductGrid extends Control
{

	public function __construct(
		private ?int $categoryId,
		private readonly ProductsFacade $productsFacade,
	) {}

	public function render(): void
	{
		$this->getTemplate()->products =  $this->productsFacade->findProducts(['order' => 'title']);

		$this->getTemplate()->setFile(__DIR__ . '/templates/grid.latte');
		$this->getTemplate()->render();
	}

	public function handleEdit(int $productId): void
	{
		$this->presenter->redirect('Product:edit', ['productId' => $productId]);
	}

	public function handleDelete(int $productId): void {
		try {
			$this->productsFacade->delete($this->productsFacade->getProduct($categoryId));
			$this->presenter->flashMessage('Product úspěšně smazána', 'info');
		} catch (\Throwable $e) {
			$this->presenter->flashMessage('Nepodařilo se smazat product', 'danger');
		}

		$this->presenter->redirect('Product:default');
	}
}

