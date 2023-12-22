<?php declare(strict_types=1);

namespace App\AdminModule\Presenters;

use App\AdminModule\Components\PermissionEditForm\PermissionGrid;
use App\AdminModule\Components\PermissionGrid\PermissionGridFactory;
use App\AdminModule\Components\ResourceEditForm\ResourceEditForm;
use App\AdminModule\Components\ResourceEditForm\ResourceEditFormFactory;
use App\AdminModule\Components\ResourceGrid\ResourceGrid;
use App\AdminModule\Components\ResourceGrid\ResourceGridFactory;
use App\Model\Entities\Resource;
use App\Model\Facades\ResourcesFacade;
use Nette\DI\Attributes\Inject;

class ResourcesPresenter extends BasePresenter {

	#[Inject]
	public ResourcesFacade $resourcesFacade;

	#[Inject]
	public ResourceGridFactory $resourceGrid;

	#[Inject]
	public ResourceEditFormFactory $resourceEditForm;

	private ?Resource $resource = null;

	public function renderDefault(): void {}

	public function actionEdit(?string $resourceId = null): void
	{
		$this->template->edit = false;

		if ($resourceId !== null) {
			$this->resource = $this->resourcesFacade->get($resourceId);
			$this->template->edit = true;
		}
	}

	protected function createComponentResourceEditForm(): ResourceEditForm
	{
		return $this->resourceEditForm->create($this->resource, true);
	}

	protected function createComponentResourcesGrid(): ResourceGrid
	{
		return $this->resourceGrid->create();
	}
}
