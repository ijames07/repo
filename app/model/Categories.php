<?php

namespace App\Model;

use Nette;

class Categories extends \Nette\Object {
	/** @var Nette\Database\Context */
	private $database;
	
	public function __construct(Nette\Database\Context $database) {
		$this->database = $database;
	}
	
	/** @return Nette\Database\Table\Selection */
	public function getAll() {
		return $this->database->table("category");
	}
	
	/** @return Nette\Database\Table\ActiveRow */
	public function add($name) {
		return $this->database->table('category')->insert(array(
			'name' => $name
		));
	}
	
	/** @return boolean */
	public function updateCategory($name = "", $id = 0) {
		return $this->database->table('category')
				->where('id', $id)
				->update(array(
					'name' => $name
				));
	}
	
	/** @return Nette\Database\Table\ActiveRow */
	public function get($id) {
		return $this->database->table('category')->get($id);
	}
	
	public function toggle($id = '') {
		if ($id == '') {
			return;
		}
		$cat = $this->database->table('category')
				->where ('id', $id)
				->fetch();
		if (empty($cat)) {
			return;
		}
		return $this->database->table('category')
				->where('id', $id)
				->update(array(
					'active' => $cat->active ? false : true
				));
	} 
}