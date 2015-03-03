<?php

namespace App\Model;

use Nette;

class Orders extends \Nette\Object {
	/** @var Nette\Database\Context */
	private $database;
	
	public function __construct(Nette\Database\Context $database) {
		$this->database = $database;
	}
	
	/** @return Nette\Database\Table\ActiveRow */
	public function insertOrder($cust_id, $prod_id, $pickup) {
		return $this->database->table('order')->insert(array(
			'customer_id' => $cust_id,
			'product_id' => intval($prod_id),
			'pickup_time' => date('c', strtotime($pickup))
		));
	}
	
	/** @return Nette\Database\Table\Selection */
	public function getAll() {
		return $this->database->table('order')->order('solved DESC, creation_date DESC');
	}
	
	/** @return Nette\Database\Table\ActiveRow */
	public function getOrder($id = 0) {
		return $this->database->table('order')->get($id);
	}
}