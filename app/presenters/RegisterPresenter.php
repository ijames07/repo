<?php

namespace App\Presenters;

use Nette\Application\UI,
	Nette\Application\UI\Form as Form,
	Nette\Mail\Message,
	Nette\Mail\SendmailMailer,
	Instante\Bootstrap3Renderer\BootstrapRenderer;

class RegisterPresenter extends BasePresenter {

	protected function startup() {
		parent::startup();
		if ($this->getUser()->isLoggedIn()) {
			$this->redirect('Orders:');
		}
	}

	public function renderDefault() {
		
	}

	protected function createComponentRegisterForm() {
		$form = new Form;
		$form->setRenderer(new BootstrapRenderer);
		$form->addText('name', 'Jméno')
				->addRule(Form::FILLED, 'Zadejte své jméno');
		$form->addText('surname', 'Příjmení')
				->addRule(Form::FILLED, 'Zadejte své příjmení');
		$form->addText('email', 'E-mail', 35)
				->setEmptyValue('@')
				->addRule(Form::FILLED, 'Zadejte svůj email')
				->addCondition(Form::FILLED)
				->addRule(Form::EMAIL, 'Zadaný text nemá správný formát emailu');
		$form->addPassword('password', 'Heslo', 20)
				->setOption('description', 'Alespoň 6 znaků')
				->addRule(Form::FILLED, 'Vyplňte Vaše heslo')
				->addRule(Form::MIN_LENGTH, 'Heslo musí mít alespoň %d znaků.', 6);
		$form->addPassword('password2', 'Heslo znovu', 20)
				->addConditionOn($form['password'], Form::VALID)
				->addRule(Form::FILLED, 'Stejné heslo ještě jednou pro kontrolu')
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
		$result = $this->context->usersService->add($values);
		if ($result == false) {
			$this->flashMessage('Registrace se nezdařila, opakujte to prosím později', 'error');
		} else if ($result === -1) {
			$this->flashMessage('Zadaný email je již zaregistrován, zkuste se přihlásit', 'warning');
		} else {
			$this->flashMessage('Registrace se zdařila, na Váš email byl zaslán email s aktivačním kódem.', 'success');
		}
		$this->sendMail($values->email);
		$this->redirect('Sign:in');
	}
	
	public function actionActivate($mail, $hash) {
		if (empty($mail) || empty($hash)) {
			$this->flashMessage('Neznámy email pro aktivaci', 'error');
			$this->redirect('Homepage:');
		}
		if (strcmp(sha1($mail . 'traktor'), $hash) == 0) {
			$result = $this->context->usersService->activate($mail);
			if ($result == 1) {
				$this->flashMessage('Aktivace účtu proběhla úspěšně', 'success');
			}
		} else {
			$this->flashMessage('Špatný kontrolní součet pro kontrolu', 'error');	
		}
		$this->redirect('Homepage:');
	}
	
	private function sendMail($user) {
		$mail = new Message();
		$hash = sha1($user . 'traktor');
		$mail->setFrom('cat@cafe.cz')
				->addTo($user)
				->setSubject('Aktivace účtu Cat Café')
				->setBody("Dobrý den,\nvaše registrace byla přijata. Pro aktivac"
						. "i účtu klikněte prosím na následující odkaz:\n" . 
						$this->link('//Register:activate', array($user, $hash))
				);
		$mailer = new SendmailMailer;
		$mailer->send($mail);
	}
}
