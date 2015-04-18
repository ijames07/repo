<?php

namespace App\Presenters;

use Nette,
	App\Model;

/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends Nette\Application\UI\Presenter {
	use \IPub\MobileDetect\TMobileDetect;
	/** categories for menu */
	private $categories;
	
	/**
     * When working with jquery mobile ui, trick nette into thinking that nothing is ajax, when in fact everything
     * *is* ajax. The reason for it: we need to redirect with http codes, not with snippet-updating mechanism, which
     * should be disabled for all requests - jquery mobile framework handles these things on it's own.
     *
     * @return bool
     */
    public function isAjax() {
        if (!$this->mobileDetect->isMobile() && $this->getUser()->isInRole('customer')) {
			return false;
		} else {
			parent::isAjax();
		}
    }
	
	protected function startup() {
		parent::startup();
		if ($this->getUser()->isInRole('manager')) {
			$this->template->categories = $this->context->getService('categoriesService')->getAll();
		} else {
			$this->template->categories = $this->context->getService('categoriesService')->getActive();
		}
	}
	
	protected function createTemplate($class = NULL) {
        // Init template
        $template = parent::createTemplate($class);

        // Add mobile detect and its helper to template
        $template->_mobileDetect    = $this->mobileDetect;
        $template->_deviceView      = $this->deviceView;

        return $template;
    }
	
	protected function beforeRender() {
		// pracovni slozka je app/templates/
		if ($this->mobileDetect->isMobile() && $this->getUser()->isInRole('customer')) { // if mobile, set mobile templates
			$this->setView($this->getView() . '.mobile');
			$this->setLayout('layout.mobile');
		} else {
			$this->setView($this->getView());
			$this->setLayout('layout');
		}
	}
}
