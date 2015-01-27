<?php

namespace App\Presenters;

use Nette,
	App\Model;

/**
 * Products presenter
 */
class ProductsPresenter extends BasePresenter {

	/** @var Model\Products */
	private $products;

	public function __construct(Model\Products $products) {
		$this->products = $products;
	}

	protected function startup() {
		parent::startup();
		if (!getUser()->isLoggedIn()) {
			if ($this->user->logoutReason === Nette\Security\IUserStorage::INACTIVITY) {
				$this->flashMessage('You have been signed out due to inactivity. Please sign in again.');
			}
			$this->redirect('Sign:in', array('backlink' => $this->storeRequest()));
		}
		echo "test";
	}

	public function renderDefault() {

	}

}
