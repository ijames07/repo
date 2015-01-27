<?php

namespace App\Presenters;

use Nette,
	App\Model,
	Instante\Bootstrap3Renderer\BootstrapRenderer;

/**
 * Sign in/out presenters.
 */
class SignPresenter extends BasePresenter {

	/**
	 * Sign-in form factory.
	 * @return Nette\Application\UI\Form
	 */
	protected function createComponentSignInForm() {
		$form = new Nette\Application\UI\Form;
		$form->setRenderer(new BootstrapRenderer);
		$form->addText('username', 'Uživatelské jméno:')
				->setRequired('Zadej prosím uživatelské jméno');

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
			$this->redirect("Products:");
		}
	}

	public function signInFormSucceeded($form, $values) {
		if ($values->remember) {
			$this->getUser()->setExpiration('14 days', FALSE);
		} else {
			$this->getUser()->setExpiration('20 minutes', TRUE);
		}

		try {
			$this->getUser()->login($values->username, $values->password);
			$this->flashMessage("Přihlášení proběhlo úspěšně");
			$this->redirect('Products:');
		} catch (Nette\Security\AuthenticationException $e) {
			$form->addError($e->getMessage());
		}
	}

	public function actionOut() {
		$this->getUser()->logout();
		$this->flashMessage('Byl jsi odhlášen.');
		$this->redirect('in');
	}

}
