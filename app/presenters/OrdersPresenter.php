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
	
	public function renderDefault($id = 1) {
		$orders = $this->context->ordersService;
		$user_id = $this->getUser()->getId();
		if ($this->getUser()->isInRole('customer')) {
			$this->template->prepared = $orders->getPrepared($user_id);
			$this->template->cancelled = $orders->getCancelled($user_id);
			$this->template->picked = $orders->getPicked($user_id);
			$this->template->unpicked = $orders->getUnpicked($user_id);
			$this->template->opened = $orders->getOpened($user_id);
		} else if($this->getUser()->isInRole('manager')) {
			$orders = $this->context->getService('ordersService');
			$paginator = new Nette\Utils\Paginator;
			$paginator->setItemCount(count($orders->getAll())); // celkový počet rezervací
			$paginator->setItemsPerPage(15); // počet položek na stránce
			$paginator->setPage($id); // číslo aktuální stránky, číslováno od 1
			$this->template->orders = $orders->getAll($paginator);
			$this->template->paginator = $paginator;
		} else {
			$this->template->orders = $this->context->ordersService->getOrdersForProcessing();
		}
	}
	
	public function actionEdit($id) {
		$post = $this->request->getPost();
		if ((empty($id) && empty($post)) || !$this->getUser()->isInRole('manager')) {
			$this->redirect('Orders:');
		}
		if (!empty($id)) {
			$this->template->order = $this->context->ordersService->getOrder($id);
		} else {
			$values = array('order_id' => $post["order"], 'solved' => $post["time"]);
			$result = $this->context->ordersService->updateOrder($values);
			if ($result == 1) {
				$this->flashMessage('Objednávka změněna', 'success');
			} else {
				$this->flashMessage('Nepodařilo se upravit', 'error');
			}
			$this->redirect('Orders:');
		}
	}
	
	/*protected function createComponentOrderForm() {
		$form = new Form;
		$form->setRenderer(new \Instante\Bootstrap3Renderer\BootstrapRenderer);
		$form->addTbDateTimePicker('solved', 'Datum a čas dokončení')
			->setRequired();
		$form->addHidden('order_id');
		$form->addSubmit('send', 'Změnit');
		$form->onSuccess[] = callback($this, 'orderFormSuccess');
		return $form;
	}*/
	/*
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
		
	}*/
	
	public function renderCreateOrder($prod, $pickup) {
		$order = $this->context->orderService
				->insertOrder($this->getUser()->getId(), $prod, $pickup);
		$this->flashMessage('Požadavek na přípravu byl zaregistrován, vyzvědněte si jej v ' . $pickup, 'info');
		$this->redirect('Orders:');
	}
	
	public function actionPrepare() {
		$id = $this->request->getPost('id')["id"];
		if (!$this->getUser()->isInRole('employee') || !$this->isAjax() || empty($id)) {
			$this->terminate();
		}
		$this->context->ordersService->prepare($id, $this->getUser()->getId());
		$this->terminate();
		/*if ($result == 1) {
			$this->flashMessage('Připravuji', 'success');
		} else {
			$this->flashMessage('Chyba při přípravě', 'error');
		}
		$this->redirect('Orders:');*/
	}
	
	public function actionFinish() {
		$id = $this->request->getPost('id')["id"];
		if (!$this->getUser()->isInRole('employee') || empty($id) || !$this->isAjax()) {
			$this->terminate();
		}
		$this->context->ordersService->finish($id);
		$this->terminate();
		/*if ($result == 1) {
			$this->flashMessage('Dokončeno', 'success');
		} else {
			$this->flashMessage('Chyba dokončení', 'error');
		}
		$this->redirect('Orders:');*/
	}
	
	public function actionRefresh() {
		//$last = $this->request->getPost('ts')['ts'];
		if (!$this->isAjax() || !$this->getUser()->isInRole('employee')) {
			$this->terminate();
		}
		//$now = new \Nette\Utils\DateTime();
		$orders = $this->context->ordersService->getOrdersForProcessing();
		$response = array();
		foreach ($orders as $order) {
			array_push($response, array(
				'id'	=> $order->id,
				'employee'	=> !empty($order->employee_id) ? $order->ref('employee_id')->surname : '',
				'product'	=> $order->ref('product_id')->name,
				'img_ext'	=> $order->ref('product_id')->img_ext,
				'customer'	=> $order->ref('customer_id')->email,
				'product_id'	=> $order->product_id,
				'customer_id'	=> $order->customer_id,
				'creation_date'	=> date('G:i', $order->creation_date->getTimestamp()),
				'pickup_time'	=> date('G:i', $order->pickup_time->getTimestamp()),
			));
		}
		$this->sendResponse(new \Nette\Application\Responses\JsonResponse($response));
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
