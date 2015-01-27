<?php

namespace App\Presenters;

use Nette\Application\UI,
	Nette\Application\UI\Form as Form,
	Instante\Bootstrap3Renderer\BootstrapRenderer;

class RegisterPresenter extends BasePresenter {

	/** @var \App\model\UserManager @inject * */
	public $userManager;

	protected function startup() {
		parent::startup();
	}

	public function renderRegister() {
		
	}

	protected function createComponentRegisterForm() {
		$form = new Form;
		$form->setRenderer(new BootstrapRenderer);
		$form->addText('name', 'Jméno')
				->addRule(Form::FILLED, 'Zadejte své jméno');
		$form->addText('surname', 'Příjmení')
				->addRule(Form::FILLED, 'Zadejte své příjmení');
		$form->addText('email', 'E-mail: *', 35)
				->setEmptyValue('@')
				->addRule(Form::FILLED, 'Zadejte svůj email')
				->addCondition(Form::FILLED)
				->addRule(Form::EMAIL, 'Zkontrolujte svůj zadaný email, zadaná adresa není email');
		$form->addPassword('password', 'Heslo: *', 20)
				->setOption('description', 'Alespoň 6 znaků')
				->addRule(Form::FILLED, 'Vyplňte Vaše heslo')
				->addRule(Form::MIN_LENGTH, 'Heslo musí mít alespoň %d znaků.', 6);
		$form->addPassword('password2', 'Heslo znovu: *', 20)
				->addConditionOn($form['password'], Form::VALID)
				->addRule(Form::FILLED, 'Heslo znovu')
				->addRule(Form::EQUAL, 'Hesla se neshodují.', $form['password']);
		$sex = array(
			1 => 'muž',
			0 => 'žena'
		);
		$form->addRadioList('gender', 'Pohlaví', $sex)
				->addRule(Form::FILLED, "Vyber své pohlaví");
		$form->addCheckbox('newsletter', "Přejete si zasílat informace o nabídkách?");
		$form->addSubmit('send', 'Registrovat');
		$form->onSuccess[] = callback($this, 'registerFormSubmitted');
		return $form;
	}

	public function registerFormSubmitted(UI\Form $form) {
		$values = $form->getValues();
		$new_user_id = $this->userManager->add($values);
		if ($new_user_id) {
			$this->flashMessage('Registrace se zdařila, jo!');
			$this->redirect('Sign:in');
		}
	}

}
