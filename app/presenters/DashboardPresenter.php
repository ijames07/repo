<?php

namespace App\Presenters;

use Nette;

/**
 * Products presenter
 */
class DashboardPresenter extends BasePresenter {
	
	protected function startup() {
		parent::startup();
		if (!$this->getUser()->isLoggedIn()) {
			if ($this->user->logoutReason === Nette\Security\IUserStorage::INACTIVITY) {
				$this->flashMessage('Byl jsi automaticky odhlášen z důvodu neaktivity. Přihlaš se prosím znovu.');
			}
			$this->redirect('Sign:in', array('backlink' => $this->storeRequest()));
		}
		if (!$this->getUser()->isInRole('manager')) {
			$this->redirect('Orders:');
		}
	}
	
	public function actionDefault() {
		$this->template->popularProducts = $this->context->getService('ordersService')
				->getPopularProducts();
		$this->template->popularDesks = $this->context->getService('bookingsService')
				->getPopularDesks();
		$this->template->smokingTF = $this->context->getService('bookingsService')
				->getPopularDeskType();
		$this->template->productsF = $this->context->getService('ordersService')
				->getFemalePopularProducts();
		$this->template->productsM = $this->context->getService('ordersService')
				->getMalePopularProducts();
	}
}