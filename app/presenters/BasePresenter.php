<?php

namespace App\Presenters;

use Nette,
	App\Model;
	//IPub\MobileDetect\TMobileDetect;

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
	/*
	 protected function createTemplate($class = NULL) {
        // Init template
        $template = parent::createTemplate($class);

        // Add mobile detect and its helper to template
        $template->_mobileDetect    = $this->mobileDetect;
        //$template->_deviceView      = $this->deviceView;

        return $template;
    }*/
}
