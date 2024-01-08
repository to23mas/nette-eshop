<?php declare(strict_types=1);

namespace App\AdminModule\Components\ResourceEditForm;

use App\Model\Entities\Resource;
use App\Model\Facades\ResourcesFacade;
use Error;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;

final class ResourceEditForm extends Control
{

	public function __construct(
		private ?Resource $resource,
		private bool $create,
		private readonly ResourcesFacade $resourcesFacade,
	) {}

	public function render(): void
	{
		$this->getTemplate()->setFile(__DIR__ . '/templates/form.latte');
		$this->getTemplate()->render();
	}

	public function createComponent(string $name): Form
	{
		$form = new Form;

		$form->addText('resourceId');

		if ($this->resource !== null) {
			$form->setDefaults([
				'resourceId' => $this->resource->resourceId,
			]);
		}

		$form->addSubmit('submit', 'Uložit');
		$form->addSubmit('submitAndStay', 'Uložit a zůstat');

		$form->onSuccess[] = [$this, 'handleFormSubmitted'];
		return $form;
	}

	/**
	 * @throws Error
	 */
	public function handleFormSubmitted(Form $form, ResourceFormData $formData): void
	{
		if ($this->resource === null) { //create
			try {
				$this->resourcesFacade->create($formData->resourceId);
				$this->presenter->flashMessage('Resource úspěšně uložen', 'info');
			} catch (\Throwable $e) {
				$this->presenter->flashMessage('Nepodařilo se uložit resource', 'danger');

				return;
			}

			$this->resource = $this->resourcesFacade->get($formData->resourceId);

			/** @var SubmitButton $submitAndStay */
			$submitAndStay = $form['submitAndStay'];

			$submitAndStay->isSubmittedBy()
				? $this->presenter->redirect('Resources:edit', ['resourceId' => $formData->resourceId])
				: $this->presenter->redirect('Resources:default')
			;
		} else {
			try {
				$this->resourcesFacade->update($this->resource->resourceId, $formData->resourceId);
				$this->presenter->flashMessage('Resource úspěšně uložen', 'info');
			} catch (\Throwable $e) {
				$this->presenter->flashMessage('Nepodařilo se upravit resource', 'danger');
				return;
			}


			$this->resource = $this->resourcesFacade->get($formData->resourceId);

			/** @var SubmitButton $submitAndStay */
			$submitAndStay = $form['submitAndStay'];

			if ($this->create) {
				$submitAndStay->isSubmittedBy()
					? $this->presenter->redirect('Resources:edit', ['resourceId' => $formData->resourceId])
					: $this->presenter->redirect('Resources:default');
			} else {
				$submitAndStay->isSubmittedBy()
					? $this->presenter->redirect('Permissions:editResource', ['resourceId' => $formData->resourceId])
					: $this->presenter->redirect('Permissions:default', ['roleId' => null]);
			}
		}
	}

}

