<?php

namespace App\Presenters;

use Nette,
	App\Model,
	Nette\Application\UI\Form as Form;

/**
 * Categories presenter
 */
class CategoriesPresenter extends BasePresenter {
	
	/** @var Model\Categories 
	private $categories;

	public function __construct(Model\Categories $categories) {
		$this->categories = $categories;
	}*/

	protected function startup() {
		parent::startup();
		if (!$this->getUser()->isLoggedIn()) {
			if ($this->user->logoutReason === Nette\Security\IUserStorage::INACTIVITY) {
				$this->flashMessage('You have been signed out due to inactivity. Please sign in again.');
			}
			$this->redirect('Sign:in', array('backlink' => $this->storeRequest()));
		}
	}

	public function actionDefault() {
		$this->template->categories = $this->context->categoriesService->getAll()->order('name');
	}
	
	public function actionAdd() {
		$this->template->products = $this->context->productsService->getAll()->order("name");
		$products = array();
		foreach ($this->template->products as $product => $row) {
			$products[$row->id] = $row->name;
		}
		$this["categoryForm"]["products"]->setItems($products);
	}
	
	public function actionEdit($id = 0) {
		if ($id == 0) {
			$this->redirect('Products:categories:');
		}
		$category = $this->context->categoriesService->get($id);
		$this->template->products = $this->context->productsService->getAll()->order("name");
		$products = array();
		foreach ($this->template->products as $product => $row) {
			$products[$row->id] = $row->name;
		}
		$current_products = $this->context->productsService->getProductsFromCategory($category->name);
		$current = array();
		foreach ($current_products as $product) {
			array_push($current, $product->id);
		}
		unset($current_products);
		$this["categoryForm"]["products"]->setItems($products);
		$this["categoryForm"]->setDefaults(array(
			'name' => $category->name
		));
		$this["categoryForm"]->setDefaults(['products' => $current]);
		$this["categoryForm"]->addHidden('category_id', $id);
	}
	
	protected function createComponentCategoryForm() {
		$form = new Form;
		$form->setRenderer(new \Instante\Bootstrap3Renderer\BootstrapRenderer);
		$form->addText('name', 'Název kategorie')
				->addRule(Form::FILLED, 'Zadejte prosím název přidávané kategorie');
		$form->addCheckboxList('products', 'Produkty patřící k této kategorii');
		$form->addSubmit('send');
		$form->onSuccess[] = callback($this, 'categoryFormSubmitted');
		return $form;
	}
	
	public function categoryFormSubmitted(Form $form) {
		$values = $form->getValues();
		if (isset($values["category_id"])) {
			$category = $this->context->categoriesService->updateCategory($values["name"], $values["category_id"]);
		} else {
			$category = $this->context->categoriesService->add($values["name"]);
		}
		$category_id = isset($values["category_id"]) ? $values["category_id"] : $category->id;
		$current_category_products = $this->context->productsService->getProductsFromCategory($values["name"]);
		$current = array();
		foreach ($current_category_products as $category => $row) {
			array_push($current, $row->id);
		}
		unset($current_category_products);
		$add = array_diff($values["products"], $current);
		$rem = array_diff($current, $values["products"]);
		foreach ($add as $product) {
			$this->context->productsService->addCategoryToProduct($product, $category_id);
		}
		foreach ($rem as $product) {
			$this->context->productsService->removeCategoryFromProduct($product, $category_id);
		}
	}
}
