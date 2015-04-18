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
			TABLE_CAT_PROD = 'category_product',
			COLUMN_IMG_EXT = "img_ext",
			COLUMN_CAT = 'category_id',
			COLUMN_PROD = 'product_id',
			COLUMN_MANAGER = 'manager_id',
			COLUMN_PRICE = "price",
			COLUMN_TIME = 'preparation_time',
			COLUMN_NAME = "name",
			COLUMN_URI = "uri",
			COLUMN_ACTIVE = 'active',
			COLUMN_ID = "id";
	
	public function __construct(Nette\Database\Context $database) {
		$this->database = $database;
	}
	
	/** @return Nette\Databse\Table\ActiveRow */
	public function getProduct($product = 0) {
		return $this->database->table(self::TABLE_NAME)->get($product);
	}
	
	/** @return Nette\Database\Table\Selection */
	public function getAll() {
		return $this->database->table(self::TABLE_NAME)->order(self::COLUMN_NAME);
	}
	
	/** @return Nette\Database\Table\Selection */
	public function getActive() {
		return $this->database->table(self::TABLE_NAME)
				->where(self::TABLE_NAME . '.' . self::COLUMN_ACTIVE, true)
				->order(self::COLUMN_NAME);
	}
	
	/** @return Nette\Database\Table\ActiveRow */
	public function addProduct($product) {
		return $this->database->table(self::TABLE_NAME)
				->insert($product);
	}
	
	/** @return Nette\Database\Table\ActiveRow */
	public function addCategoryToProduct($product_id, $category_id) {
		return $this->database->table(self::TABLE_CAT_PROD)->insert(array(
			self::COLUMN_CAT	=> $category_id,
			self::COLUMN_PROD	=> $product_id
		));
	}
	
	/** @return int */
	public function removeCategoryFromProduct($product_id, $category_id) {
		return $this->database->table(self::TABLE_CAT_PROD)
				->where(self::COLUMN_PROD, $product_id)
				->where(self::COLUMN_CAT, $category_id)
				->delete();
	}

	/** @return boolean */
	public function updateProduct($product) {
		return $this->database->table(self::TABLE_NAME)
				->where(self::COLUMN_ID, $product["id"])
				->update($product);
	}
	
	/** @return Nette\Database\Table\Selection */
	public function getCategories($product_id = 0) {
		return $this->database->table(self::TABLE_CAT_PROD)
				->where(self::COLUMN_PROD, $product_id)
				->order(self::COLUMN_CAT);
	}
}