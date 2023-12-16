<?php declare(strict_types = 1);

namespace App\AdminModule\Components\Sidebar;

use Nette\Application\UI\Control;
use Nette\Http\Session;

final class Sidebar extends Control
{

	/** @var array<string, array<string, mixed|>> */
	private array $sidebar = [
		'Dashboard' => [
			'icon' => 'fa fa-dashboard',
			'link' => 'Dashboard:',
			'pesenter' => 'Dashboard:*',
		],
		'Produkty' => [
			'icon' => 'fa fa-shopping-cart',
			'link' => 'Product:',
			'presenter' => 'Product:*',
		],
		'Kategorie' => [
			'icon' => 'fa fa-file',
			'link' => 'Category:',
			'presenter' => 'Category:*',
		],
		'Objednávky' => [
			'icon' => 'fa fa-vcard',
			'link' => '',
			'presenter' => 'i',
		],
		'Users' => [
			'icon' => 'fa fa-users',
			'link' => '',
			'presenter' => 'i',
		],
	];

	public function render(): void
	{
		$this->getTemplate()->sidebar = $this->sidebar;
		$this->getTemplate()->setFile(__DIR__ . '/templates/sidebar.latte');
		$this->getTemplate()->render();
	}
}
