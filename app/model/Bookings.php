<?php

namespace App\Model;

use Nette;

class Bookings extends \Nette\Object {
	/** @var Nette\Database\Context */
	private $database;
	
	const TABLE_BOOKING = 'booking',
			TABLE_TABLE = 'table',
			COLUMN_ID = 'id',
			COLUMN_TIME = 'time';
	
	public function __construct(Nette\Database\Context $database) {
		$this->database = $database;
	}

	/** @return Nette\Database\Selection */
	public function getTables($timestamp = 0) {
		if ($timestamp == 0) {
			return $this->database->table('table')->order(self::COLUMN_ID);
		}
		return $this->database->table(self::TABLE_BOOKING)->where(self::COLUMN_TIME . ' BETWEEN ? AND ?',
				date('c', strtotime('-2 hours', $timestamp), 
				date('c', strtotime('+2 hours', $timestamp))));
	}
}