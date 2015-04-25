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
	private $categories;
	private $products;

	protected function startup() {
		parent::startup();
		$this->categories = $this->context->categoriesService;
		$this->products = $this->context->productsService;
		if (!$this->getUser()->isLoggedIn()) {
			if ($this->user->logoutReason === Nette\Security\IUserStorage::INACTIVITY) {
				$this->flashMessage('Byl jsi automaticky odhlášen z důvodu neaktivity. Pro pokračování se prosím přihlaš znovu.');
			}
			$this->redirect('Sign:in', array('backlink' => $this->storeRequest()));
		}
	}

	public function actionDefault() {
		if ($this->getUser()->isInRole('customer')) {
			$this->template->categories = $this->categories
					->getActive()
					->order('name');
		} else {
			$this->template->categories = $this->categories
					->getAll()
					->order('name');
		}
	}
	
	public function actionAdd() {
		if (!$this->getUser()->isInRole('manager')) {
			$this->redirect('Categories:');
		}
		$this->template->products = $this->products
				->getAll()
				->order("name");
		$products = array();
		foreach ($this->template->products as $product => $row) {
			$products[$row->id] = $row->name;
		}
		$this["categoryForm"]["products"]->setItems($products);
		$this["categoryForm"]->addSubmit('send', 'Přidat kategorii');
	}
	
	public function actionEdit($id = 0) {
		if ($id == 0 || !$this->getUser()->isInRole('manager')) {
			$this->redirect('Categories:');
		}
		// ziskani dat o kategorii
		$this->template->category = $this->categories->get($id);
		// ziskani produktu pro naplneni checkboxu ve formulari
		$this->template->products = $this->products->getAll()->order("name");
		$products = array();
		// naplneni checkboxu ve formulari
		foreach ($this->template->products as $product => $row) {
			$products[$row->id] = $row->name;
		}
		$current_products = $this->products
				->getAll()
				->where(':category_product.category.id', $this->template->category->id);
		$current = array();
		foreach ($current_products as $product) {
			array_push($current, $product->id);
		}
		unset($current_products);
		$this["categoryForm"]["products"]->setItems($products);
		$this["categoryForm"]->setDefaults(array(
			'name' => $this->template->category->name
		));
		$this["categoryForm"]->setDefaults(['products' => $current]);
		$this["categoryForm"]->addHidden('category_id', $id);
		$this["categoryForm"]->addSubmit('send', 'Upravit kategorii');
	}
	
	protected function createComponentCategoryForm() {
		$form = new Form;
		$form->setRenderer(new \Instante\Bootstrap3Renderer\BootstrapRenderer);
		$form->addText('name', 'Název kategorie')
				->addRule(Form::FILLED, 'Zadejte prosím název přidávané kategorie');
		$form->addCheckboxList('products', 'Produkty patřící k této kategorii');
		$form->onSuccess[] = callback($this, 'categoryFormSubmitted');
		return $form;
	}
	
	public function categoryFormSubmitted(Form $form) {
		$values = $form->getValues();
		if (isset($values["category_id"])) {
			$category = $this->categories->updateCategory($values["name"], $values["category_id"]);
			if ($category == 1) {
				$this->flashMessage('Kategorie byla úspěšně upravena', 'success');
			} else {
				$this->flashMessage('Kategorii se nepovedlo upravit', 'error');
			}
		} else {
			$category = $this->categories->add($values["name"]);
			if ($category != false) {
				$this->flashMessage('Kategorie byla úspěšně přidána', 'success');
			} else {
				$this->flashMessage('Kategorii se nepovedlo přidat', 'error');
			}
		}
		$category_id = isset($values["category_id"]) ? $values["category_id"] : $category->id;
		$current_category_products = $this->products->getAll()
				->where(':category_product.category.id', $values["category_id"]);
		$current = array();
		foreach ($current_category_products as $category => $row) {
			array_push($current, $row->id);
		}
		unset($current_category_products);
		$add = array_diff($values["products"], $current);
		$rem = array_diff($current, $values["products"]);
		foreach ($add as $product) {
			$this->products->addCategoryToProduct($product, $category_id);
		}
		foreach ($rem as $product) {
			$this->products->removeCategoryFromProduct($product, $category_id);
		}
		$this->redirect('Categories:');
	}
	
	public function actionToggle($id = '') {
		if ($id != '' && $this->getUser()->isInRole('manager')) {
			$this->categories->toggle($id);
		}
		$this->flashMessage('Změněno', 'success');
		$this->redirect('Categories:');
	}
	
}
