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

	/** Pro uložení dat o produktu pro formuláře */
	private $product;
	
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
		$this->template->products = $this->context->productsService->getAll()->order('name');
	}
	
	public function renderCategories($name = '') {
		if ($name == '') {
			$this->template->categories = $this->context->categoriesService->getAll()->order("name");
			$this->template->products = array();
			$this->template->title = 'Všechny kategorie';
		} else {
			$this->template->products = $this->context->productsService->getProductsFromCategory($name);
			$this->template->title = $name;
			$this->template->categories = array();
		}
	}
	
	public function renderProduct($id = '') {
		$this->template->product = $this->context->productsService->getProduct($id);
		$this->product = $this->template->product;
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
		$form->addHidden("id", $this->product["id"]);
		$form->addSubmit("send", "Připravit");
		$form->onSuccess[] = callback($this, "orderFormSubmitted");
		return $form;
	}
	
	public function orderFormSubmitted(Form $form) {
		$values = $form->getValues();
		$inserted = $this->context->ordersService->insertOrder($this->getUser()->getId(), $values["id"], $values["time"]);
		if ($inserted) {
			$this->flashMessage("Objednávka byla zaregistrována. "
					. "Nezapomeňte si ji dnes přijít vyzvednout v " . date("G:i", strtotime($values["time"])), 'success');
			$this->redirect('Orders:');
		}
	}
	
	public function actionEdit($product = 0) {
		if ($product == 0) {
			$this->redirect("Products:");
		}
		$this->product = $this->template->product = $this->context->productsService->getProduct($product);
		$this->template->categories = $this->context->categoriesService->getAll()->order("name");
		$allCategories = $this->context->categoriesService->getAll()->order('name');
		$pCategories = $this->context->productsService->getCategories($this->product->id);
		$this->template->title = $this->template->product->name . ' úprava';
		$raw = array();
		foreach ($allCategories as $category) {
			$raw[$category->id] = $category->name;
		}
		unset($allCategories);
		$this["productForm"]->setDefaults(array(
			'name'			=> $this->product->name,
			'description'	=> $this->product->description,
			'ingredients'	=> $this->product->ingredients,
			'price'			=> $this->product->price,
		));
		$this["productForm"]["categories"]->setItems($raw);
		$this["productForm"]->addHidden('product_id', $this->product->id);
		$productCategory = array();
		foreach ($pCategories as $category => $row) {
			array_push($productCategory, $row->category_id);
		}
		unset($pCategories);
		$this["productForm"]->setDefaults(['categories' => $productCategory]);
	}
		
	public function actionAdd() {
		$this->template->title = 'Přidání produktu';
		$allCategories = $this->context->categoriesService->getAll()->order('name');
		$categories = array();
		foreach ($allCategories as $category => $row) {
			$categories[$row->id] = $row->name;
		}
		$this["productForm"]["categories"]->setItems($categories);
		$this["productForm"]["img"]->setRequired();
	}
	
	protected function createComponentProductForm() {
		$form = new Form;
		$form->setRenderer(new BootstrapRenderer);
		$form->addText('name', 'Název')
				->addRule(Form::FILLED, 'Zadejte prosím název produktu');
		$form->addText('description', 'Popis')
				->addRule(Form::FILLED, 'Popište prosím produkt podrobněji');
		$form->addText('ingredients', 'Složení')
				->addRule(Form::FILLED, "Napište prosím složení produktu");
		$form->addText('price', 'Cena')
				->addRule(Form::FILLED, "Napište prodejní cenu");
		$form->addUpload('img', 'Obrázek')
				->addCondition(Form::FILLED)
				->addRule(Form::IMAGE, 'obrazek')
				->addRule(Form::MAX_FILE_SIZE, 'Maximální povolená velikost obrázku je 10 MB',
						10 * 1024 * 1024 /* v B */);
		$form->addCheckboxList('categories', 'Kategorie');
		$form->addSubmit('send');
		$form->addProtection();
		$form->onSuccess[] = callback($this, 'productFormSubmitted');
		return $form;
	}
	
	public function productFormSubmitted(Form $form) {
		$values = $form->getValues();
		$data = array(
			'ingredients'	=> $values["ingredients"],
			'description'	=> $values["description"],
			'price'			=> $values["price"],
			'name'			=> $values["name"],
			'uri'			=> Nette\Utils\Strings::webalize($values["name"]),
		);
		
		if ($values["img"]->isOk()) {
			$name = explode('.', $values["img"]->getName());
			$data['img_ext'] = array_pop($name);			
		}
		
		$current_cat = array(); // current product categories
		if (isset($values["product_id"])) {
			$data["id"] = $values["product_id"];
			$product = $this->context->productsService->updateProduct($data);
			$categories = $this->context->productsService->getCategories($values["product_id"]);
			foreach ($categories as $category => $row) {
				array_push($current_cat, $row->category_id);
			}
			unset($categories);
		} else {
			$product = $this->context->productsService->addProduct($data);
		}

		// sprava kategorii
		$add = array_diff($values["categories"], $current_cat);
		$rem = array_diff($current_cat, $values["categories"]);
		$product_id = isset($values["product_id"]) ? $values["product_id"] : $product->id;
		foreach ($add as $category) {
			$this->context->productsService->addCategoryToProduct($product_id, $category);
		}
		foreach ($rem as $category) {
			$this->context->productsService->removeCategoryFromProduct($product_id, $category);
		}
		// sprava obrazku
		if (!$values["img"]->isOk()) {
			return;
		}
		try {
			$fileName = $product_id . '.' . $data["img_ext"];
			$fileTarget = 'images/' . $fileName;
			$values["img"]->move($fileTarget);
			$image = Nette\Utils\Image::fromFile($fileTarget);
			$image->resize(480, 480);
			$image->save('images/products/' . $product_id . '.' . $data["img_ext"]);
			$image->resize(80, 80);
			$image->save('images/products/' . $product_id . 'n.' . $data["img_ext"]);
			$fileExts = array_diff(['gif', 'png', 'jpg'], array($data["img_ext"]));
			while(false) {}
			foreach ($fileExts as $fileExt) {
				@unlink('images/products/' . $product_id . '.' . $fileExt);
				@unlink('images/products/' . $product_id . 'n.' . $fileExt);
			}
			unlink($fileTarget);
		} catch (Exception $e) {
			throw new Exception($e);
		}
	}
}
