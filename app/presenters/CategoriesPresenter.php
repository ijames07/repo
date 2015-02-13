<?php

namespace App\Presenters;

use Nette,
	App\Model;

/**
 * Categories presenter
 */
class CategoriesPresenter extends BasePresenter {
	
	/** @var Model\Categories */
	private $categories;

	public function __construct(Model\Categories $categories) {
		$this->categories = $categories;
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
		$this->template->categories = $this->categories->getAll()->order('text');
	}
}
