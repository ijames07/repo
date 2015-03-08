<?php

namespace App\Model;

use Nette;

class Products extends \Nette\Object {
	
	/** @var Nette\Database\Context */
	private $database;
	
	const
			COLUMN_DESCRIPTION = "description",
			COLUMN_INGREDIENTS = "ingredients",
			TABLE_NAME = "product",
			COLUMN_IMG_EXT = "img_ext",
			COLUMN_PRICE = "price",
			COLUMN_NAME = "name",
			COLUMN_URI = "uri",
			COLUMN_ID = "id";
	
	public function __construct(Nette\Database\Context $database) {
		$this->database = $database;
	}
	
	/** @return Nette\Database\Table\Selection */
	public function getProductsFromCategory($name) {
		return  $this->database->table(self::TABLE_NAME)
				->where(':category_product.category.name', $name);
	}
	
	/** @return Nette\Databse\Table\ActiveRow */
	public function getProduct($product = 0) {
		return $this->database->table(self::TABLE_NAME)->get($product);
	}
	
	/** @return Nette\Database\Table\Selection */
	public function getAll() {
		return $this->database->table(self::TABLE_NAME);
	}
	
	/** @return Nette\Database\Table\ActiveRow */
	public function addProduct($product) {
		return $this->database->table(self::TABLE_NAME)->insert($product);
	}
	
	/** @return Nette\Database\Table\ActiveRow */
	public function addCategoryToProduct($product_id, $category_id) {
		return $this->database->table('category_product')->insert(array(
			'category_id'	=> $category_id,
			'product_id'	=> $product_id
		));
	}
	
	/** @return Nette\Database\Table\ActiveRow */
	public function removeCategoryFromProduct($product_id, $category_id) {
		return $this->database->table('category_product')
				->where('product_id', $product_id)
				->where('category_id', $category_id)
				->delete();
	}

	/** @return boolean */
	public function updateProduct($product) {
		return $this->database->table(self::TABLE_NAME)
				->where('id', $product["id"])
				->update($product);
	}
	
	/** @return Nette\Database\Table\Selection */
	public function getCategories($product_id = 0) {
		return $this->database->table('category_product')
				->where('product_id', $product_id);
	}
}