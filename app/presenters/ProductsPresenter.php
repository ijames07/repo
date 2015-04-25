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
	private $products;
		
	protected function startup() {
		parent::startup();
		$this->products = $this->context->productsService;
		if (!$this->getUser()->isLoggedIn()) {
			if ($this->user->logoutReason === Nette\Security\IUserStorage::INACTIVITY) {
				$this->flashMessage('You have been signed out due to inactivity. Please sign in again.');
			}
			$this->redirect('Sign:in', array('backlink' => $this->storeRequest()));
		}
	}

	public function actionDefault($cat = '') {
		if ($cat == '') {
			// vypis vsech produktu
			if ($this->getUser()->isInRole('manager')) {
				$this->template->products = $this->products
						->getAll()
						->order('name');
			} else {
				$this->template->products = $this->products
						->getActive()
						->order('name');
			}
		} else {
			// vypis produktu v kategorii $cat
			if ($this->getUser()->isInRole('manager')) {
				$this->template->products = $this->products
						->getAll()
						->where(':category_product.category.id', $cat)
						->order('name');
			} else {
				$this->template->products = $this->products
						->getActive()
						->where(':category_product.category.id', $cat)
						->order('name');
			}
			$this->template->category = $this->context->categoriesService
					->get($cat)
					->name;
		}
	}
	
	public function actionProduct($id = '') {
		if ($id == '') {
			$this->redirect('Products:');
		}
		// ziskam info o produktu
		$this->product = $this->products->getProduct($id);
		$now = new Nette\Utils\DateTime();
		// zohledneni doby pripravy do nejkratsi doby objednavky
		// jestli priprava trva dyl jak 15 minut tak nastav aktualni hodnotu na 15 minut
		if ($this->product->preparation_time * 60 + 5 * 60 > 15 * 60) {
			$current = $this->product->preparation_time * 60 + 5 * 60;
		} else {
			// jinak nastav aktualni hodnotu na dobu pripravy + 5 minut
			$current = 15 * 60;
		}
		$min = $this->product->preparation_time * 60 + 5 * 60;
		$this["orderForm"]["time"]
				->setAttribute('min', date('H:i', $now->getTimestamp() + $min))
				->setDefaultValue(date('H:i', $now->getTimestamp() + $current));
		$this->template->product = $this->product;
	}
	
	protected function createComponentOrderForm() {
		$form = new Form;
		$form->setRenderer(new BootstrapRenderer);
		$form->addText('time', "Čas vyzvednutí")
				->setType('time')
				->addRule(Form::FILLED, "Vlož čas vyzvednutí");
		$form->addHidden("id", $this->product["id"]);
		$form->addSubmit("send", "Připravit");
		$form->onSuccess[] = callback($this, "orderFormSuccess");
		return $form;
	}
	
	public function orderFormSuccess(Form $form) {
		$values = $form->getValues();
		$active = $this->getUser()->getIdentity()->__get('active');
		if (!$active) {
			$this->flashMessage('Váš účet ještě není aktivován. Aktivační odkaz najdete v emailu', 'error');
			$this->redirect('Orders:');
		}
		$blocked = $this->getUser()->getIdentity()->__get('blocked');
		if ($blocked) {
			$this->flashMessage('Váš účet byl zablokován z důvodu nevyzvedávání produktů. Pro další info kontaktujte zaměstnance.', 'error');
			$this->redirect('Orders:');
		}
		$time = strtotime($values["time"]);
		$product = $this->context->getService('productsService')->get($values["id"]);
		if ($product == false) {
			$this->flashMessage('Neznámý produkt', 'error');
			$this->redirect('Products:');
		}
		if ($time  < strtotime('now') + $product->preparation_time * 60) {
			$this->flashMessage('Na tento čas nelze připravit produkt', 'error');
			$this->redirect('Products:');
		}
		$inserted = $this->context->ordersService->insertOrder(
				$this->getUser()->getId(),
				$values["id"],
				$values["time"]
			);
		if ($inserted) {
			$this->flashMessage("Objednávka byla zaregistrována. "
				. "Nezapomeňte si ji dnes přijít vyzvednout v " . date("G:i",
				strtotime($values["time"])), 'success');
			$this->redirect('Orders:');
		}
	}
	
	public function actionMobileOrderSubmitted() {
		$post = $this->request->getPost();
		if (empty($post["time"]) || empty($post["id"])) {
			$this->flashMessage('Nepodařilo se objednat produkt', 'error');
			$this->redirect('Products:');
		}
		$active = $this->getUser()->getIdentity()->__get('active');
		if (!$active) {
			$this->flashMessage('Váš účet ještě není aktivován. Aktivační odkaz najdete v emailu', 'error');
			$this->redirect('Orders:');
		}
		$blocked = $this->getUser()->getIdentity()->__get('blocked');
		if ($blocked) {
			$this->flashMessage('Váš účet byl zablokován z důvodu nevyzvedávání produktů. Pro další kontaktujte zaměstnance.', 'error');
			$this->redirect('Orders:');
		}
		$inserted = $this->context->ordersService->insertOrder(
				$this->getUser()->getId(),
				$post["id"],
				$post["time"]
			);
		if ($inserted) {
			$this->flashMessage("Objednávka byla zaregistrována. "
				. "Nezapomeňte si ji dnes přijít vyzvednout v " . date("G:i",
				strtotime($post["time"])), 'success');
			$this->redirect('Orders:');
		}
	}
	
	public function actionEdit($product = 0) {
		if ($product == 0) {
			$this->redirect("Products:");
		}
		$this->product = $this->template->product = $this->products
				->getProduct($product);
		$this->template->categories = $this->context->categoriesService
				->getAll()
				->order("name");
		$allCategories = $this->context->categoriesService
				->getAll()
				->order('name');
		$pCategories = $this->products->getCategories($this->product->id);
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
			'time'			=>$this->product->preparation_time
		));
		$this["productForm"]["categories"]->setItems($raw);
		$this["productForm"]->addCheckbox('active', 'Je produkt v nabídce?')
				->setOption('visibility', 'Nastavení viditelnosti pro zákazníky');
		if ($this->product->active) {
			$this["productForm"]["active"]->setDefaultValue(TRUE);
		} else {
			$this["productForm"]["active"]->setDefaultValue(FALSE);
		}
		$this["productForm"]->addHidden('product_id', $this->product->id);
		$this["productForm"]->addSubmit('send', 'Upravit');
		$productCategory = array();
		foreach ($pCategories as $category => $row) {
			array_push($productCategory, $row->category_id);
		}
		unset($pCategories);
		$this["productForm"]->setDefaults(['categories' => $productCategory]);
	}
		
	public function actionAdd() {
		if (!$this->getUser()->isInRole('manager')) {
			$this->redirect('Products:');
		}
		$allCategories = $this->context->categoriesService
				->getAll()
				->order('name');
		$categories = array();
		foreach ($allCategories as $category => $row) {
			$categories[$row->id] = $row->name;
		}
		$this["productForm"]["categories"]->setItems($categories);
		$this["productForm"]["img"]->setRequired();
		$this["productForm"]->addSubmit('send', 'Přidat produkt');
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
		$form->addText('time', 'Doba přípravy v min')
				->addRule(Form::FILLED, "Napište kolik minut potřebujete pro přípravu");
		$form->addUpload('img', 'Obrázek')
				->addCondition(Form::FILLED)
				->addRule(Form::IMAGE, 'obrazek')
				->addRule(Form::MAX_FILE_SIZE, 'Maximální povolená velikost obrázku je 10 MB',
						10 * 1024 * 1024 /* v B */);
		$form->addCheckboxList('categories', 'Kategorie');
		$form->addProtection();
		$form->onSuccess[] = callback($this, 'productFormSuccess');
		return $form;
	}
	
	public function productFormSuccess(Form $form) {
		$values = $form->getValues();
		$data = array(
			'ingredients'	=> $values["ingredients"],
			'manager_id'	=> $this->getUser()->getId(),
			'description'	=> $values["description"],
			'price'			=> str_replace(',', '.', $values["price"]),
			'preparation_time'=> $values["time"],
			'name'			=> $values["name"],
			'active'		=> $values["active"] ? true : false,
			'uri'			=> Nette\Utils\Strings::webalize($values["name"]),
		);
		
		if ($values["img"]->isOk()) {
			$name = explode('.', $values["img"]->getName());
			$data['img_ext'] = array_pop($name);			
		}
		
		$current_cat = array(); // current product categories
		if (isset($values["product_id"])) {
			$data["id"] = $values["product_id"];
			$data["manager_id"] = $this->getUser()->getId();
			$product = $this->products->updateProduct($data);
			$categories = $this->products->getCategories($values["product_id"]);
			foreach ($categories as $category => $row) {
				array_push($current_cat, $row->category_id);
			}
			unset($categories);
		} else {
			$product = $this->products->addProduct($data);
		}

		// sprava kategorii
		$add = array_diff($values["categories"], $current_cat);
		$rem = array_diff($current_cat, $values["categories"]);
		$product_id = isset($values["product_id"]) ? $values["product_id"] : $product->id;
		foreach ($add as $category) {
			$this->products->addCategoryToProduct($product_id, $category);
		}
		foreach ($rem as $category) {
			$this->products->removeCategoryFromProduct($product_id, $category);
		}
		// sprava obrazku
		if (!$values["img"]->isOk()) {
			$this->flashMessage('Produkt byl úspěšně upraven', 'success');
			$this->redirect('Products:');
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
			foreach ($fileExts as $fileExt) {
				@unlink('images/products/' . $product_id . '.' . $fileExt);
				@unlink('images/products/' . $product_id . 'n.' . $fileExt);
			}
			unlink($fileTarget);
		} catch (Exception $e) {
			throw new Exception($e);
		}
		$this->flashMessage('Produkt byl úspěšně upraven', 'success');
		$this->redirect('Products:');
	}
}
