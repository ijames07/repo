<?php

namespace App\Presenters;

use Nette,
	App\Model,
	Nette\Application\UI\Form as Form;

/**
 * Categories presenter
 */
class PeoplePresenter extends BasePresenter {

	protected function startup() {
		parent::startup();
		if (!$this->getUser()->isLoggedIn()) {
			if ($this->user->logoutReason === Nette\Security\IUserStorage::INACTIVITY) {
				$this->flashMessage('Byl jsi automaticky odhlášen z důvodu neaktivity. Pro pokračování se prosím přihlaš znovu.');
			}
			$this->redirect('Sign:in', array('backlink' => $this->storeRequest()));
		}
		if (!$this->getUser()->isInRole('manager') && !$this->getUser()->isInRole('employee')) {
			$this->redirect('Products:');
		}
	}

	public function actionDefault($id = 1, $letter = '') {
		$users = $this->context->usersService;
		if ($letter != '') {
			$paginator = new Nette\Utils\Paginator;
			$paginator->setItemsPerPage(5); // počet položek na stránce
			if ($this->getUser()->isInRole('manager')) {
				$paginator->setItemCount(count($users->userLetter($letter))); // celkový počet položek (např. článků)
				$paginator->setPage($id); // číslo aktuální stránky, číslováno od 1
				$this->template->allUsers = $users->overallManagerInfo($paginator, $letter);
			} else {
				$poc = count($users->userLetter($letter)->where('role', 'customer'));
				$paginator->setItemCount($poc); // celkový počet položek (např. článků)
				$paginator->setPage($id); // číslo aktuální stránky, číslováno od 1
				$this->template->allUsers = $users->overallEmployeeInfo($paginator, $letter);
			}
			$this->template->paginator = $paginator;
		} else {
			$paginator = new Nette\Utils\Paginator;
			if ($this->getUser()->isInRole('manager')) {
				$paginator->setItemCount($this->context->getService('usersService')->usersCount()); // celkový počet položek (např. článků)
			} else {
				$paginator->setItemCount(count($this->context->getService('usersService')->getAll()->where('role', 'customer'))); // celkový počet položek (např. článků)
			}
			
			$paginator->setItemsPerPage(5); // počet položek na stránce
			$paginator->setPage($id); // číslo aktuální stránky, číslováno od 1
			if ($this->getUser()->isInRole('manager')) {
				$this->template->allUsers = $users->overallManagerInfo($paginator);
			} else {
				$this->template->allUsers = $users->overallEmployeeInfo($paginator, $letter);
			}
			$this->template->paginator = $paginator;
		}

	}
	
	public function actionAdd() {
		if (!$this->getUser()->isInRole('manager')) {
			$this->redirect('People:');
		}
		$users = $this->context->usersService->getAll();
		$people = array();
		$user_id = $this->getUser()->getId();
		foreach ($users as $user) {
			if ($user->id == $user_id) {
				continue;
			}
			$people[$user->id] = $user->surname . ' ' . $user->name;
		}
		$this["employeeForm"]["employee"]->setItems($people);
	}
	
	public function actionDetail($id) {
		if (empty($id)) {
			$this->redirect('People:');
		}
		
		// data k uzivateli
		$this->template->userDetails = $this->context->getService('usersService')
				->get($id);
		
		// existuje uzivatel?
		if ($this->template->userDetails == false) {
			$this->redirect('People:');
		}
		
		$orders = $this->context->getService('ordersService');
		if ($this->template->userDetails->role == 'customer') {
			$this->template->favouriteProducts = $orders->getMostOrderedProduct($id);
			$this->template->favouriteCategories = $orders->getMostOrderedCategory($id);
			$this->template->picked = $orders->getPicked($id);
			$this->template->unpicked = $orders->getUnpicked($id);
			$this->template->cancelled = $orders->getCancelled($id);
			$this->template->opened = $orders->getOpened($id);
			$this->template->unprepared = $orders->getUnprepared($id);
			$this->template->prepared = $orders->getPrepared($id);
			$this->template->bookings = $this->context->bookingsService->getAll()->where('customer_id', $id);
			if (count($this->template->picked) != 0 || count($this->template->unpicked) != 0) {
				$this->template->ratio = number_format(floatval(count($this->template->picked) / (count($this->template->picked) + count($this->template->unpicked))), 2, ",", " ");
			} else {
				$this->template->ratio = 1;
			}
		} else if ($this->template->userDetails->role == 'employee'){
			$this->template->served = $orders->getServed($this->template->userDetails->id);
		}
	}
	
	protected function createComponentEmployeeForm() {
		$form = new Form;
		$form->setRenderer(new \Instante\Bootstrap3Renderer\BootstrapRenderer);
		$form->addRadioList('role', 'Vyber typ oprávnění', array(0 => 'zaměstnanec', 1 => 'manažer', 2 => 'zákazník'))
				->addRule(Form::FILLED);
		$form->addRadioList('employee', 'Vyber uživatele, kterému chcete změnit vybrané oprávnění')
				->addRule(Form::FILLED);
		$form->addSubmit('send', 'Změnit');
		$form->onSuccess[] = callback($this, 'employeeFormSuccess');
		return $form;
	}
	
	public function employeeFormSuccess(Form $form) {
		$values = $form->getValues();
		if ($this->getUser()->isInRole('manager')) {
			$result = $this->context->usersService->changePermission($values);
			if ($result == 1) {
				$this->flashMessage('Oprávnění změněno', 'success');
			} else {
				$this->flashMessage('Chyba, nepodařilo se změnit oprávnění', 'error');
			}
		} else {
			$this->flashMessage('Nemáte oprávnění pro změnu', 'error');
		}
		$this->redirect('People:detail', $values["employee"]);
	}
	
	public function actionToggleBlocked($id) {
		if (empty($id)) {
			$this->flashMessage('Není zadáno ID uživatele pro změnu blokace', 'error');
			$this->redirect('People:');
		}
		if ($this->getUser()->isInRole('manager') || $this->getUser()->isInRole('employee')) {
			$result = $this->context->getService('usersService')->toggleBlocked($id);
			if ($result != 1) {
				$this->flashMessage('Nepodařila se změna', 'error');
				$this->redirect('People:');
			} else {
				$this->flashMessage('Změna blokace uživatele proběhla úspěšně', 'success');
				$this->redirect('People:detail', $id);
			}
		} else {
			$this->flashMessage('Zákazník nemá oprávnění ke změně blokace uživatele', 'error');
			$this->redirect('Orders:');
		}
	}
}
