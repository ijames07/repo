<?php

namespace App\Presenters;

use Nette,
	Instante\Bootstrap3Renderer\BootstrapRenderer;

/**
 * Sign in/out presenters.
 */
class SignPresenter extends BasePresenter {

	/** @persistent */
	public $backlink = '';
	
	/**
	 * Sign-in form factory.
	 * @return Nette\Application\UI\Form
	 */
	protected function createComponentSignInForm() {
		$form = new Nette\Application\UI\Form;
		$form->setRenderer(new BootstrapRenderer);
		$form->addText('username', 'Email:')
				->setRequired('Zadej prosím svůj email');

		$form->addPassword('password', 'Heslo:')
				->setRequired('Zadej prosím své heslo');

		$form->addCheckbox('remember', 'Zapamatuj si přihlášení');

		$form->addSubmit('send', 'Přihlásit');

		// call method signInFormSucceeded() on success
		$form->onSuccess[] = $this->signInFormSucceeded;
		return $form;
	}
	
	public function actionIn() {
		if ($this->getUser()->isLoggedIn()) {
			$this->redirect("Orders:");
		}
	}

	public function signInFormSucceeded($form, $values) {
		if ($values->remember) {
			$this->getUser()->setExpiration('14 days', FALSE);
		} else {
			$this->getUser()->setExpiration('20 minutes');
		}

		try {
			$this->getUser()->login($values->username, $values->password);
			$this->flashMessage("Přihlášení proběhlo úspěšně", 'success');
			$this->restoreRequest($this->backlink);
			$this->redirect('Orders:');
		} catch (Nette\Security\AuthenticationException $e) {
			$form->addError($e->getMessage());
		}
	}

	public function actionOut() {
		$this->getUser()->logout();
		$this->flashMessage('Byl jsi odhlášen.', 'info');
		$this->redirect('Homepage:');
	}

}
