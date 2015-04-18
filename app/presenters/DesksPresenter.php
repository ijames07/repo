<?php

namespace App\Presenters;

use Nette,
	Nette\Application\UI\Form as Form,
	App\Model;

class DesksPresenter extends BasePresenter {
	
	protected function startup() {
		parent::startup();
		if (!$this->getUser()->isLoggedIn()) {
			if ($this->user->logoutReason === Nette\Security\IUserStorage::INACTIVITY) {
				$this->flashMessage('You have been signed out due to inactivity. Please sign in again.');
			}
			$this->redirect('Sign:in', array('backlink' => $this->storeRequest()));
		}
		if (!$this->getUser()->isInRole('manager')) {
			$this->flashMessage('Nemáš oprávnění na změnu stolů', 'error');
			$this->redirect('Bookings:');
		}
	}
	
	public function actionDefault() {
		$this->template->desks = $this->context->getService('desksService')->getAll();
	}
	
	protected function createComponentDeskForm() {
		$form = new Form;
		$form->setRenderer(new \Instante\Bootstrap3Renderer\BootstrapRenderer);
		$form->addText('seats', 'Míst k sezení')
				->setAttribute('type', 'number')
				->addRule(Form::FILLED, 'Napiš počet míst k sezení!');
		$form->addCheckbox('smoking', 'Kuřácký');
		$form->addCheckbox('indoor', 'Vnitřní');
		$form->onSuccess[] = callback($this, 'deskFormSuccess');
		return $form;
	}
	
	public function actionEdit($id) {
		if (empty($id)) {
			$this->flashMessage('Neznámý stůl pro úpravu', 'error');
			$this->redirect('Desks:');
		}
		$desk = $this->context->getService('desksService')->get($id);
		if ($desk == false) {
			$this->flashMessage('Neznámý stůl pro úpravu', 'error');
			$this->redirect('Desks:');
		}
		$this["deskForm"]->addHidden('id', $desk->id);
		$this["deskForm"]->setDefaults(array(
			'seats'		=> $desk->seats,
			'smoking'	=> $desk->smoking ? true : false,
			'indoor'	=> $desk->indoor ? true : false
		));
		$this["deskForm"]->addSubmit('send', 'Upravit');
	}
	
	public function actionAdd() {
		$this["deskForm"]->addSubmit('send', 'Přidat');
	}
	
	public function actionToggleActive($id) {
		if (!empty($id) && $this->getUser()->isInRole('manager')) {
			$this->context->getService('desksService')->toggleActive($id);
		}
		$this->flashMessage('Změněno', 'success');
		$this->redirect('Desks:');
	}
	
	public function deskFormSuccess(Form $form) {
		$values = $form->getValues();
		$desks = $this->context->getService('desksService');
		if (empty($form->id)) {
			// novy
			$desk = $desks->insertDesk($values);
			if ($desk != false) {
				$this->flashMessage('Stůl přidán', 'success');
			} else {
				$this->flashMessage('Nepodařilo se přidat stůl', 'error');
			}
		} else {
			// uprava
			$result = $desks->updateDesk($values);
			if ($result === 1) {
				$this->flashMessage('Stůl úspěšně upraven', 'success');
			} else {
				$this->flashMessage('Nepodařilo se upravit stůl', 'error');
			}
		}
		$this->redirect('Desks:');
	}
}
