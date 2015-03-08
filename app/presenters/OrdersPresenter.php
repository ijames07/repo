<?php

namespace App\Presenters;

use Nette,
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
		$this->template->orders = $this->context->ordersService->getAll()->where('customer_id = ?', $this->getUser()->getId());
	}
	
	public function renderCreateOrder($prod, $pickup) {
		$this->context->orderService->insertOrder($this->getUser()->getId(), $prod, $pickup);
		$this->flashMessage('Požadavek na přípravu byl zaregistrován, vyzvědněte si jej v ' . $pickup, 'info');
		$this->redirect('Orders:');
	}
	
	public function actionGet($id = 0) {
		if ($this->isAjax()) {
			/** @var Nette\Database\Table\ActiveRow */
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
			$this->terminate();
		}
		$this->terminate();
	}
}
