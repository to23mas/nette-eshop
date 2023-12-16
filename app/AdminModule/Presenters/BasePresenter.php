<?php

namespace App\AdminModule\Presenters;

use App\AdminModule\Components\Sidebar\Sidebar;
use App\AdminModule\Components\Sidebar\SidebarFactory;
use Nette\Application\AbortException;
use Nette\Application\ForbiddenRequestException;
use Nette\Application\UI\Presenter;
use Nette\DI\Attributes\Inject;

abstract class BasePresenter extends Presenter {

	#[Inject]
	public SidebarFactory $sidebarFactory;
	
	/**
	 * @throws ForbiddenRequestException
	 * @throws AbortException
	 */
	protected function startup(): void {
		parent::startup();
		$presenterName = $this->request->presenterName;
		$action = !empty($this->request->parameters['action'])?$this->request->parameters['action']:'';

		if (!$this->user->isAllowed($presenterName,$action)){
			if ($this->user->isLoggedIn()){
				throw new ForbiddenRequestException();
			}else{
				$this->flashMessage('Pro zobrazení požadovaného obsahu se musíte přihlásit!','warning');
				$this->redirect(':Front:User:login', ['backlink' => $this->storeRequest()]);
			}
		}
	}

	public function createComponentSidebar(): Sidebar
	{
		return $this->sidebarFactory->create();
	}
}
