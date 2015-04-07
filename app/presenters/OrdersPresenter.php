<?php

namespace App\Presenters;

use Nette,
	Nette\Application\UI\Form as Form,
	App\Model;

class OrdersPresenter extends BasePresenter {
	
	protected function startup() {
		parent::startup();
		if (!$this->getUser()->isLoggedIn()) {
			if ($this->user->logoutReason === Nette\Security\IUserStorage::INACTIVITY) {
				$this->flashMessage('You have been signed out due to inactivity. Please sign in again.');
			}
			$this->redirect('Sign:in', array('backlink' => $this->storeRequest()));
		}
	}
	
	public function renderDefault() {
		$orders = $this->context->ordersService;
		$user_id = $this->getUser()->getId();
		if ($this->getUser()->isInRole('customer')) {
			$this->template->prepared = $orders->getPrepared($user_id);
			$this->template->cancelled = $orders->getCancelled($user_id);
			$this->template->picked = $orders->getPicked($user_id);
			$this->template->unpicked = $orders->getUnpicked($user_id);
			$this->template->opened = $orders->getOpened($user_id);
		} else if($this->getUser()->isInRole('manager')) {
			$this->template->orders = $orders->getAll(0);
		}
	}
	
	public function actionEdit($id) {
		if (empty($id) || !$this->getUser()->isInRole('manager')) {
			$this->redirect('Orders:');
		}
		$this->template->order = $this->context->ordersService->getOrder($id);
		$this["orderForm"]->setDefaults(array(
			'order_id'	=>	$id
		));
	}
	
	protected function createComponentOrderForm() {
		$form = new Form;
		$form->setRenderer(new \Instante\Bootstrap3Renderer\BootstrapRenderer);
		$form->addTbDateTimePicker('solved', 'Datum a čas dokončení')
			->setRequired();
		$form->addHidden('order_id');
		$form->addSubmit('send', 'Změnit');
		$form->onSuccess[] = callback($this, 'orderFormSuccess');
		return $form;
	}
	
	public function orderFormSuccess(Form $form) {
		$values = $form->getValues();
		$values["employee_id"] = $this->getUser()->getId();
		if ($this->getUser()->isInRole('manager')) {
			$result = $this->context->ordersService->updateOrder($values);
			if ($result == 1) {
				$this->flashMessage('Objednávka změněna', 'success');
			} else {
				$this->flashMessage('Nepodařilo se upravit', 'error');
			}
		} else {
			$this->flashMessage('Nemáš oprávnění na změnu', 'error');
		}
		$this->redirect('Orders:');
		
	}
	
	public function renderCreateOrder($prod, $pickup) {
		$order = $this->context->orderService
				->insertOrder($this->getUser()->getId(), $prod, $pickup);
		$this->flashMessage('Požadavek na přípravu byl zaregistrován, vyzvědněte si jej v ' . $pickup, 'info');
		$this->redirect('Orders:');
	}
	
	public function actionGet($id = 0) {
		if ($this->isAjax()) {
			$order = $this->context->ordersService->getOrder(intval($id));
			if ($order != false) {
				$this->sendResponse(new \Nette\Application\Responses\JsonResponse(array(
					'id' => $order->id,
					'product_id' => $order->product_id
				)));
			} else {
				$this->sendResponse(new \Nette\Application\Responses\JsonResponse(array(
					'id' => 0,
					'product_id' => 0
				)));
			}
		}
		$this->terminate();
	}
	
	public function actionCancel($id) {
		$order = $this->context->getService('ordersService')
				->cancel($id, $this->getUser()->getId());
		if ($order == 1) {
			$this->flashMessage('Požadavek na přípravu byl zrušen', 'success');
		} else {
			$this->flashMessage('Požadavek nemohl být zrušen.', 'error');
		}
		$this->redirect('Orders:');
	}
}
