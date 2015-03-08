<?php

namespace App\Presenters;

use Nette,
	Instante\Bootstrap3Renderer\BootstrapRenderer,
	Nette\Application\UI\Form as Form;

/**
 * Products presenter
 */
class BookingsPresenter extends BasePresenter {
	
	private $tables;
	
	protected function startup() {
		parent::startup();
		if (!$this->getUser()->isLoggedIn()) {
			if ($this->user->logoutReason === Nette\Security\IUserStorage::INACTIVITY) {
				$this->flashMessage('Byl jsi odhlášen z důvodu neaktivity. Přihlaš se prosím znovu');
			}
			$this->redirect('Sign:in', array('backlink' => $this->storeRequest()));
		}
	}
	
	public function actionDefault() {
		$this->tables = $this->context->bookingsService->getTables()
				->order("seats")
				->order("smoking")
				->order("indoor")
				->order("id");
		$tables = array();
		foreach ($this->tables as $table) {
			$tables[$table->id] = $table->seats . ' osoby' . ($table->smoking ? '' : ', nekuřácký')
					. ($table->indoor ? "" : ", terasa");
		}
		unset($this->tables);
		$this["bookingForm"]["tables"]->setItems($tables);
	}
	
	/** @return Nette\Application\UI\Form */
	protected function createComponentBookingForm() {
		$form = new Form;

		$form->setRenderer(new BootstrapRenderer);
		/*$form->addText('time', 'Hodina')
				->setType('time')
				->setDefaultValue(date("G:i", strtotime('18:00')))
				->addRule(Form::FILLED, 'Zadej čas ve který si chceš rezervovat');*/
		$form->addTbDateTimePicker('date', 'Datum a čas rezervace')
				->setRequired();
		$form->addRadioList('tables', 'Volné stoly');
		$form->addSubmit('send', 'Rezervovat');
		$form->onSuccess[] = callback($this, 'bookingFormSubmitted');
		return $form;
	}
	
	public function bookingFormSubmitted(Form $form) {
		$values = $form->getValues();
		$this->flashMessage($values["date"], 'info');
	}
	
	public function actionFree($id = 0) {
		if (!$this->isAjax()) {
			$this->redirect('Bookings:');
		}
		if ($id == 0) {
			$this->redirect('Bookings:');
		}
		$tables = $this->context->bookingsService->getTables($id);
		$booked_tables = array();
		foreach ($tables as $table) {
			array_push($booked_tables, $table->id);
		}
		unset($tables);
		$free_tables = $this->context->bookingsService->getTables()->where('id NOT IN ?', $booked_tables);
		$response = array();
		foreach ($free_tables as $table) {
			array_push($response, array(
				'id' => $table->id,
				'seats' => $table->seats,
				'smoking' => $table->smoking,
				'indoor' => $table->indoor
			));
		}
		unset($free_tables);
		$this->sendResponse(new Nette\Application\Responses\JsonResponse($response));
		$this->terminate();
	}
}