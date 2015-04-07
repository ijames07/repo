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
				$this->flashMessage('Byl jsi automaticky odhlášen z důvodu neaktivity. Přihlaš se prosím znovu.');
			}
			$this->redirect('Sign:in', array('backlink' => $this->storeRequest()));
		}
	}
	
	public function actionDefault() {
		$this->template->followingBookings = $this->context->bookingsService
				->getFollowingBookings($this->getUser()->getId());
		$this->template->previousBookings = $this->context->bookingsService
				->getPreviousBookings($this->getUser()->getId());
	}
	
	public function actionAdd() {
		
	}
	
	/** @return Nette\Application\UI\Form */
	protected function createComponentBookingForm() {
		$form = new Form;
		$form->setRenderer(new BootstrapRenderer);
		$form->addTbDateTimePicker('date', 'Datum a čas rezervace')
				->setRequired();
		return $form;
	}
	
	public function actionCancel($id) {
		if (empty($id)) {
			$this->flashMessage('Nedostal jsem id rezervace ke zrušení', 'error');
			$this->redirect('Bookings:');
		}
		$bookings = $this->context->bookingsService;
		$result = $bookings->cancel($id, $this->getUser()->getId());
		if ($result == 1) {
			$this->flashMessage('Rezervace stornována', 'success');
		} else {
			$this->flashMessage('Rezervaci již nelze stornovat', 'error');
		}
		$this->redirect('Bookings:');
	}
	
	public function actionFree() {
		$timestamp = $this->request->getPost('timestamp')['timestamp'];
		if (!$this->isAjax() || empty($timestamp)) {
			$this->terminate();
		}
		$bookings = $this->context->bookingsService;
		$takenTables = $bookings->getReservedTables($timestamp);
		$bookedTables = array(0);
		$response = array();
		foreach ($takenTables as $table => $row) {
			array_push($bookedTables, $row->desk_id);
		}
		$freeTables = $bookings->getAllTables()
				->where('id NOT IN (?)', $bookedTables);
		foreach ($freeTables as $table => $row ) {
			array_push($response, array(
				'id'	=> $row->id,
				'seats' => $row->seats,
				'smoking'	=> $row->smoking,
				'indoor'	=> $row->indoor,
				'free'	=> true
			));
		}
		foreach ($takenTables as $table => $row) {
			array_push($bookedTables, $row->id);
			array_push($response, array(
				'id' =>		$row->ref('desk_id')->id,
				'seats' =>	$row->ref('desk_id')->seats,
				'smoking' =>$row->ref('desk_id')->smoking,
				'indoor' =>	$row->ref('desk_id')->indoor,
				'free'	=> false
			));
		}
		$this->sendResponse(new Nette\Application\Responses\JsonResponse($response));
	}
	
	public function actionBook() {
		$values = $this->request->getPost();
		if (empty($values["tables"]) || empty($values["time"])) {
			$this->redirect('Bookings:');
		}
		$table_id = intval($values["tables"]);
		$time = intval($values["time"]);
		$bookings = $this->context->bookingsService;
		$reserved = $bookings->getReservedTables($time);
		foreach($reserved as $table) {
			// kontrola obsazenosti stolu
			if ($table_id == $table->desk_id) {
				$this->redirect('Bookings:');
			}
		}
		$newBooking = $bookings->add($this->getUser()->getId(), $table_id, $time);
		if ($newBooking =! false) {
			$this->flashMessage('Zarezervováno na ' . date('j.n.Y G', $time), 'success');
		} else {
			$this->flashMessage('Chyba při rezervaci, zkuste to prosím později.', 'error');
		}
		$this->redirect('Bookings:');
	}
	
}