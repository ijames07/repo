<?php

namespace App\Model;

use Nette;

class Products extends \Nette\Object {
	
	/** @var Nette\Database\Context */
	private $database;
	
	public function __construct(Nette\Database\Context $database) {
		$this->database = $database;
	}
	
	/** @return Nette\Database\Table\ActiveRow */
	public function getProductsFromCategory($cat) {
		$products = $this->database->table('product');
		return $products->where(':category_product.category.name', $cat);


		/*return $this->database->query(''
				. 'SELECT * FROM project.products '
				. 'JOIN project.category_product USING (products_id)'
				. 'JOIN project.categories USING (categories_id) '
				. 'WHERE text = ?', $cat);*/
	}
	
	/** @return Nette\Databse\Table\ActiveRow */
	public function getProduct($product = 0) {
		return $this->database->table('project.products')->get($product);
	}
	
	/** @return Nette\Database\Table\Selection */
	public function getAll() {
		return $this->database->table("project.products");
	}
	
	/** @return Nette\Database\Table\ActiveRow */
	public function insertNew($product) {

	}
}