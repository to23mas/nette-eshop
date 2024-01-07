<?php declare(strict_types = 1);

namespace App\AdminModule\Components\Sidebar;

use Nette\Application\UI\Control;

final class Sidebar extends Control
{

	/** @var array<string, array<string, mixed|>> */
	private array $sidebar = [
		// 'Dashboard' => [
		// 	'icon' => 'fa fa-dashboard',
		// 	'link' => 'Dashboard:',
		// 	'pesenter' => 'Dashboard:*',
		// ],
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
			'icon' => 'fa fa-shopping-basket',
			'link' => '',
			'presenter' => 'i',
		],
		'Uživatelé' => [
			'icon' => 'fa fa-users',
			'link' => 'Users:default',
			'presenter' => 'Users:*',
		],
		'Přístupy' => [
			'icon' => 'fa fa-id-card',
			'link' => 'Permissions:default',
			'presenter' => 'Permissions:*',
		],
		'Resources' => [
			'icon' => 'fa fa-address-book-o',
			'link' => 'Resources:default',
			'presenter' => 'Resources:*',
		],
	];

	public function render(): void
	{
		$this->getTemplate()->sidebar = $this->sidebar;
		$this->getTemplate()->setFile(__DIR__ . '/templates/sidebar.latte');
		$this->getTemplate()->render();
	}
}
