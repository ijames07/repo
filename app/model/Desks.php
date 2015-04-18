<?php

namespace App\Model;

use Nette;

class Desks extends \Nette\Object {
	
	/** @var Nette\Database\Context */
	private $database;
	
	const
			TABLE_NAME = 'desk',
			COLUMN_SMOKING = 'smoking',
			COLUMN_SEATS = 'seats',
			COLUMN_INDOOR = 'indoor',
			COLUMN_ACTIVE = 'active',
			COLUMN_ID = 'id';
	
	public function __construct(Nette\Database\Context $database) {
		$this->database = $database;
	}
	
	/** @return Nette\Database\Table\Selection */
	public function getAll() {
		return $this->database->table(self::TABLE_NAME)
				->order(self::COLUMN_ID);
	}
	
	/** @return \Nette\Database\Table\ActiveRow */
	public function get($id) {
		if (empty($id)) {
			return;
		}
		return $this->database->table(self::TABLE_NAME)->get($id);
	}
	
	/** @return Nette\Database\Table\ActiveRow */
	public function insertDesk($data) {
		if (empty($data)) {
			return;
		}
		return $this->database->table(self::TABLE_NAME)->insert(array(
			self::COLUMN_SEATS		=> $data->seats,
			self::COLUMN_SMOKING	=> $data->smoking,
			self::COLUMN_INDOOR		=> $data->indoor
		));
	}
	
	public function updateDesk($data) {
		if (empty($data)) {
			return;
		}
		return $this->database->table(self::TABLE_NAME)
				->where(self::COLUMN_ID, $data->id)
				->update($data);
	}
	
	/** @return int */
	public function toggleActive($id) {
		if (empty($id)) {
			return;
		}
		$desk = $this->database->table(self::TABLE_NAME)->get($id);
		if ($desk == false) {
			return;
		}
		return $this->database->table(self::TABLE_NAME)
				->where(self::COLUMN_ID, $id)
				->update(array(
					self::COLUMN_ACTIVE	=> $desk->active ? false : true
				));
	}

}