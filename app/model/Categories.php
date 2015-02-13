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
		return $this->database->table("project.categories");
	}
}