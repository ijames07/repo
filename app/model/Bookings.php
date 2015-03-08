<?php

namespace App\Model;

use Nette;

class Bookings extends \Nette\Object {
	/** @var Nette\Database\Context */
	private $database;
	
	public function __construct(Nette\Database\Context $database) {
		$this->database = $database;
	}

	/** @return Nette\Database\Selection */
	public function getTables($timestamp = 0) {
		if ($timestamp == 0) {
			return $this->database->table('table');
		}
		return $this->database->table('booking')->where('time BETWEEN ? AND ?',
				date('c', strtotime('-2 hours', $timestamp), 
				date('c', strtotime('+2 hours', $timestamp))));
	}
}