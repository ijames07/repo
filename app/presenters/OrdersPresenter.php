<?php

namespace App\Presenters;

use Nette,
 App\Model;

class OrdersPresenter extends BasePresenter {
	
	/** @var type Model\Orders */
	private $orders;

	public function __construct(Model\Orders $orders) {
		$this->orders = $orders;
	}
	
	protected function startup() {
		parent::startup();
		if (!$this->getUser()->isLoggedIn()) {
			if ($this->user->logoutReason === Nette\Security\IUserStorage::INACTIVITY) {
				$this->flashMessage('Byl jsi odhlášen z důvodu neaktivity. Přihlaš se prosím znovu.');
			}
			$this->redirect('Sign:in', array('backlink' => $this->storeRequest()));
		}
	}
	
	public function renderDefault() {
		$this->template->orders = $this->orders->getAll()->where('customers_id = ?', $this->getUser()->getId());
	}
	
	public function renderCreateOrder($prod, $pickup) {
		$this->orders->insertOrder($this->getUser()->getId(), $prod, $pickup);
		$this->flashMessage('Požadavek na přípravu byl zaregistrován, vyzvědněte si jej v ' . $pickup, 'info');
		$this->redirect('Orders:');
	}
}
