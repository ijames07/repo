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
		if (!$this->getUser()->isLoggedIn()) {
			if ($this->user->logoutReason === Nette\Security\IUserStorage::INACTIVITY) {
				$this->flashMessage('You have been signed out due to inactivity. Please sign in again.');
			}
			$this->redirect('Sign:in', array('backlink' => $this->storeRequest()));
		}
	}

	public function renderDefault() {
		$this->template->products = $this->products->getAll()->order('$name');
	}
	
	public function renderCategory($cat = 0) {
		$this->template->products = $this->products->getProductsFromCategory($cat);
	}
	
	public function renderProduct($id = 0) {
		$this->template->product = $this->products->getProduct($id);
	}
}
