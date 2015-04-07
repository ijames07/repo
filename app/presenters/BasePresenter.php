<?php

namespace App\Presenters;

use Nette,
	App\Model;

/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends Nette\Application\UI\Presenter {
	
	/** categories for menu */
	private $categories;
	
	protected function startup() {
		parent::startup();
		if ($this->getUser()->isInRole('manager')) {
			$this->template->categories = $this->context->getService('categoriesService')->getAll();
		} else {
			$this->template->categories = $this->context->getService('categoriesService')->getActive();
		}
	}
}
