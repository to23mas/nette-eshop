<?php declare(strict_types = 1);

namespace App\AdminModule\Components\Sidebar;

use Nette\Application\UI\Control;
use Nette\Http\Session;

final class Sidebar extends Control
{

	/** @var array<string, array<string, mixed|>> */
	private array $sidebar = [
		'overview' => [
			'icon' => 'fa-solid fa-circle-info fa-lg',
			'title' => 'Overview',
			'resource' => null,
			'submenu' => [
				['title' => 'System status', 'link' => 'SystemStatu:'],
				['title' => 'Database status', 'link' => 'DatabaseStatus:'],
			],
		],
		'users' => [
			'icon' => 'fa-solid fa-users fa-lg',
			'title' => 'Users',
			'submenu' => [
				['title' => 'List', 'link' => 'Users:'],
				['title' => 'LDAP settings', 'link' => 'LdapSettings:'],
				['title' => 'System Groups', 'link' => 'SystemGroups:'],
				['title' => 'Database groups', 'link' => 'DatabaseGroups:'],
				['title' => 'LDAP Groups', 'link' => 'LdapGroups:'],
			],
		],
	];

	private bool $isVisible = true;

	public function __construct(
		private Session $session
	) {}

	public function handleToggleVisibility(): void
	{
		$this->isVisible = !$this->isVisible;
	}

	public function render(): void
	{
		$this->getTemplate()->isVisible = $this->isVisible;
		$this->getTemplate()->sidebar = $this->sidebar;
		$this->getTemplate()->setFile(__DIR__ . '/templates/sidebar.latte');
		$this->getTemplate()->render();
	}
}
