<?php

namespace App\Presenters;

use Nette,
	Instante\Bootstrap3Renderer\BootstrapRenderer,
	Nette\Application\UI\Form as Form,
	App\Model;

/**
 * Products presenter
 */
class ProductsPresenter extends BasePresenter {

	/** @var Model\Products */
	private $products;
	
	private $products_id;
	
	public function __construct(Model\Products $products) {
		$this->products = $products;
	}

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
		$this->template->products = $this->products->getAll()->order('name');
	}
	
	public function renderCategory($name = '') {
		if ($name == '') {
			$this->template->products = $this->products->getAll()->order("name");
		} else {
			$this->template->products = $this->products->getProductsFromCategory($name);
			$this->template->title = $name;
		}
	}
	
	public function renderProduct($id = 0) {
		$this->template->product = $this->products->getProduct($id);
		$this->products_id = $this->template->product->products_id;
		$this->template->title = $this->template->product->name;
	}
	
	protected function createComponentOrderForm() {
		$form = new Form;
		$form->setRenderer(new BootstrapRenderer);
		$form->addText('time', "Čas vyzvednutí")
				->setType('time')
				// 15 min = 15 * 60 s
				->setDefaultValue(date("G:i", time() + 15 * 60))
				->addRule(Form::FILLED, "Vlož čas vyzvednutí");
		$form->addHidden("id", $this->products_id);
		$form->addSubmit("send", "Připravit");
		$form->onSuccess[] = callback($this, "orderFormSubmitted");
		return $form;
	}
	
	public function orderFormSubmitted(Form $form) {
		$values = $form->getValues();
		$this->redirect('Orders:createOrder', $values["id"], $values["time"]);
	}
	
	public function renderEdit($product = 0) {
		if ($product == 0) {
			$this->redirect("Products:");
		}
		$this->template->product = $this->products->getProduct($product);
		$this->template->title = $this->template->product->name . ' úprava';
	}
	
	public function renderAdd() {
		$this->template->title = 'Přidání produktu';
		
	}
	
	protected function createComponentAddForm() {
		$form = new Form;
		$form->setRenderer(new BootstrapRenderer);
		$form->addText('name', 'Název')
				->addRule(Form::FILLED, 'Zadejte prosím název produktu')
				->addCondition(Form::FILLED);
		$form->addText('description', 'Popis');
		$form->addText('ingredients', 'Složení');
		$form->addText('price', 'Cena');
		$form->addUpload('img', 'Obrázek')
				->addRule(Form::FILLED, 'Vyberte obrázek z disku')
				->addRule(Form::IMAGE, 'Obrázek musí být PNG, JPEG nebo GIF')
				->addRule(Form::MAX_FILE_SIZE, 'Maximální povolená velikost obrázku je 1 MB', 1024 * 1024 /* v B */);
		$form->addSubmit('send', 'Přidat produkt');
		$form->onSuccess = callback($this, 'addProductFormSubmitted');
		return $form;
	}
	
	private function addProductFormSubmitted(UI\Form $form) {
		$values = $form->getValues();
		$this->context->categoryService->newProduct($values);
	}
}
