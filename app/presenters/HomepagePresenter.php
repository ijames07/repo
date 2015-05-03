<?php

namespace App\Presenters;

use Nette,
	App\Model;

/**
 * Homepage presenter.
 */
class HomepagePresenter extends BasePresenter {

	public function actionDefault() {
		if ($this->mobileDetect->isMobile()) {
			$this->redirect('Sign:in');
		}
	}

}
