<?php declare(strict_types=1);

namespace App\AdminModule\Components\PermissionEditForm;

use App\Model\Entities\Permission;
use App\Model\Entities\Role;
use App\Model\Facades\PermissionsFacade;
use App\Model\Facades\ResourcesFacade;
use App\Model\Facades\RoleFacade;
use App\Model\Facades\UsersFacade;
use Error;
use Nette;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;

final class PermissionEditForm extends Control
{

	public function __construct(
		private ?Permission $permission,
		private ?Role $role,
		private readonly RoleFacade $roleFacade,
		private readonly ResourcesFacade $resourcesFacade,
		private readonly PermissionsFacade $permissionsFacade,
	) {}

	public function render(): void
	{
		$this->getTemplate()->setFile(__DIR__ . '/templates/edit.latte');
		$this->getTemplate()->render();
	}

	public function createComponent(string $name): Form
	{
		$form = new Form;

		$roleField = $form->addSelect('roleId', null, $this->findRoles());

		$form->addSelect('resourceId', null, $this->findResources())
			->setDefaultValue($this->permission?->resourceId);

		$form->addText('action');

		$typeField = $form->addSelect('type', null, ['allow' => 'allow', 'deny' => 'deny'])
			->setDefaultValue($this->permission->type);

		if ($this->permission !== null) { // editing
			$form->setDefaults([
				'action' => $this->permission->action,
			]);

			$roleField->setDisabled()
				->setOmitted(false)
				->setDefaultValue($this->permission->roleId);
		}

		if ($this->role !== null) { //creating new for $this->role
			$form->setDefaults([
				'action' => $this->permission->action,
			]);

			$roleField->setDisabled()
				->setOmitted(false)
				->setDefaultValue($this->role->roleId);
		}

		$form->addSubmit('submit', 'Uložit');
		$form->addSubmit('submitAndStay', 'Uložit a zůstat');

		$form->onSuccess[] = [$this, 'handleFormSubmitted'];
		return $form;
	}

	public function handleFormSubmitted(Form $form, PermissionFormData $formData): void
	{
		if ($this->permission === null) { // create
			$permission = new Permission([
				'roleId' => $formData->roleId,
				'resourceId' => $formData->resourceId,
				'action' => $formData->action,
				'type' => $formData->type,
			]);

			try {
				if ($this->permissionsFacade->save($permission)) {
					$this->presenter->flashMessage('Permissions úspěšně vutvořeno', 'info');
				} else {
					$this->presenter->flashMessage('Nepodařilo se vytvořit permission', 'danger');
				}
			} catch (\Exception) {
				$this->presenter->flashMessage('Již existuje', 'danger');
			}

		} else { // edit
			$this->permission->resourceId = $formData->resourceId;
			$this->permission->action = $formData->action;
			$this->permission->type = $formData->type;

			if ($this->permissionsFacade->save($this->permission)) {
				$this->presenter->flashMessage('Permissions úspěšně upraveno', 'info');
			} else {
				$this->presenter->flashMessage('Nepodařilo se upravit permission', 'danger');
			}

			/** @var SubmitButton $submitAndStay */
			$submitAndStay = $form['submitAndStay'];

			$submitAndStay->isSubmittedBy()
				? $this->redirect('this')
				: $this->presenter->redirect('Permissions:default', ['roleId' => $formData->roleId]);
		}
	}

	private function findResources(): array
	{
		$resourceIds = [];
		$allRes = $this->resourcesFacade->find();

		foreach ($allRes as $res) {
			$resourceIds[$res->resourceId] = $res->resourceId;
		}

		return $resourceIds;
	}

	private function findRoles(): array
	{
		$resourceIds = [];
		$allRes = $this->roleFacade->find();

		foreach ($allRes as $res) {
			$resourceIds[$res->roleId] = $res->roleId;
		}

		return $resourceIds;
	}
}
