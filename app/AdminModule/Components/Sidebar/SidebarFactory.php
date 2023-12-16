<?php declare(strict_types=1);

namespace App\AdminModule\Components\Sidebar;

interface SidebarFactory {

	public function create(): Sidebar;
}
