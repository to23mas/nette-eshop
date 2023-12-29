<?php declare(strict_types=1);

namespace App\AdminModule\Components\UserEditForm;

use App\Model\Entities\User;
use App\Model\Facades\RoleFacade;
use App\Model\Facades\UsersFacade;
use Error;
use Nette;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\SmartObject;

final class UserEditForm extends Control {

	use SmartObject;

	public function __construct(
		private ?User $user,
		private readonly RoleFacade $roleFacade,
		private readonly UsersFacade $usersFacade,
	) {}

	public function render(): void
	{
		$this->getTemplate()->setFile(__DIR__ . '/templates/edit.latte');
		$this->getTemplate()->render();
	}

	public function createComponent(string $name): Form
	{
		$form = new Form;

		$form->addText('username');

		$form->addEmail('email');

		$form->addSelect('role', items: $this->findRoles())
			->setDefaultValue($this->user->role->roleId);


		$form->addSubmit('submit', 'Uložit');
		$form->addSubmit('submitAndStay', 'Uložit a zůstat');

		if ($this->user !== null) {
			$form->setDefaults([
				'username' => $this->user->name,
				'email' => $this->user->email,
			]);
		}

		$form->onSuccess[] = [$this, 'handleFormSubmitted'];
		return $form;
	}

	/**
	 * @throws Error
	 */
	public function handleFormSubmitted(Form $form, UserFormData $formData): void
	{
		/** @var SubmitButton $submitAndStay */
		$submitAndStay = $form['submitAndStay'];

		/** @var SubmitButton $submit */
		$submit = $form['submit'];

		match (true) {
			$submit->isSubmittedBy() => $this->editUser($form, $formData),
			$submitAndStay->isSubmittedBy() => $this->editUser($form, $formData, true),
			default => throw new Error,
		};
	}

	private function editUser(Form $form, UserFormData $formData, bool $redirect = false): void
	{
		$this->user->name = $formData->username;
		$this->user->email = $formData->email;
		$this->user->role = $this->roleFacade->get($formData->role);
		$this->presenter->flashMessage('Uživatel byl úspěšně upraven', 'info');
		$this->usersFacade->saveUser($this->user);

		$redirect ? $this->presenter->redirect('this') : $this->presenter->redirect('Users:default');
	}

	private function findRoles(): array
	{
		$rolesIds = [];
		$allRoles = $this->roleFacade->find();

		foreach ($allRoles as $role) {
			$rolesIds[$role->roleId] = $role->roleId;
		}

		return $rolesIds;
	}
}

