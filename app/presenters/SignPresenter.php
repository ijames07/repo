<?php

namespace App\Presenters;

use Nette,
	Nette\Application\UI\Form,
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
				->addRule(Form::FILLED, 'Zadej prosím svůj email')
				->setRequired('Zadej prosím svůj email');

		$form->addPassword('password', 'Heslo:')
				->addRule(Form::FILLED, 'Zadej prosím své heslo')
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
		
	public function actionDefault() {
		$this->redirect('Sign:in');
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

	public function actionMobileSignSubmitted() {
		$post = $this->request->getPost();
		if (empty($post["username"]) || empty($post["password"])) {
			$this->flashMessage('Nepovedlo se přihlásit', 'error');
			$this->redirect('Sign:in');
		}
		if (!empty($post["permanent"]) && $post["permanent"] == 'on') {
			$this->getUser()->setExpiration('14 days', FALSE);
		} else {
			$this->getUser()->setExpiration('20 minutes');
		}
		
		try {
			$this->getUser()->login($post["username"],$post["password"]);
			if ($this->getUser()->isLoggedIn()) {
				$this->flashMessage('Přihlášení proběhlo úspěšně', 'success');
				$this->restoreRequest($this->backlink);
				$this->redirect('Orders:');
			} else {
				$this->flashMessage('Přihlášení se nezdařilo', 'error');
				$this->redirect('Sign:in');
			}
		} catch (Nette\Security\AuthenticationException $e) {
			$form->addError($e->getMessage());
		}
	}
	
}
