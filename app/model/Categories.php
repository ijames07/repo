<?php

namespace App\Model;

use Nette;

class Categories extends \Nette\Object {
	/** @var Nette\Database\Context */
	private $database;
	
	const
		TABLE_NAME = 'category',
		COLUMN_ID = 'id',
		COLUMN_ACTIVE = 'active',
		COLUMN_URI = 'uri',
		COLUMN_NAME = 'name';
	
	public function __construct(Nette\Database\Context $database) {
		$this->database = $database;
	}
	
	/** @return Nette\Database\Table\Selection */
	public function getAll() {
		return $this->database->table(self::TABLE_NAME)->order(self::COLUMN_NAME);
	}
	
	/** @return Nette\Database\Table\Selection */
	public function getActive() {
		return $this->database->table(self::TABLE_NAME)
				->where(self::COLUMN_ACTIVE, true)
				->order(self::COLUMN_NAME);
	}
	
	/** @return Nette\Database\Table\ActiveRow */
	public function add($name) {
		return $this->database->table(self::TABLE_NAME)->insert(array(
			self::COLUMN_NAME => $name,
			self::COLUMN_URI => \Nette\Utils\Strings::webalize($name)
		));
	}
	
	/** @return boolean */
	public function updateCategory($name = "", $id = 0) {
		if ($name == "" || $id == 0) {
			return;
		}
		return $this->database->table(self::TABLE_NAME)
				->where(self::COLUMN_ID, $id)
				->update(array(
					self::COLUMN_NAME => $name,
					self::COLUMN_URI => \Nette\Utils\Strings::webalize($name)
				));
	}
	
	/** @return Nette\Database\Table\ActiveRow */
	public function get($id) {
		return $this->database->table(self::TABLE_NAME)->get($id);
	}
	
	public function toggle($id = '') {
		if ($id == '') {
			return;
		}
		$cat = $this->database->table(self::TABLE_NAME)
				->where (self::COLUMN_ID, $id)
				->fetch();
		if (empty($cat)) {
			return;
		}
		return $this->database->table(self::TABLE_NAME)
				->where(self::COLUMN_ID, $id)
				->update(array(
					self::COLUMN_ACTIVE => $cat->active ? false : true
				));
	} 
}