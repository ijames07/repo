<?php

namespace App\Model;

use Nette;

class Bookings extends \Nette\Object {
	/** @var Nette\Database\Context */
	private $database;
	
	const TABLE_BOOKING = 'booking',
			TABLE_TABLE = 'desk',
			COLUMN_ID = 'id',
			COLUMN_TIME = 'time',
			COLUMN_DESK = 'desk_id',
			COLUMN_CUSTOMER = 'customer_id';
	
	public function __construct(Nette\Database\Context $database) {
		$this->database = $database;
	}
	
	/** @return Nette\Database\Table\ActiveRow */
	public function add($user, $table_id, $time) {
		if (empty($user) || empty($table_id) || empty($time)) {
			return;
		}
		$this->database->table(self::TABLE_BOOKING)->insert(array(
			self::COLUMN_CUSTOMER	=>	$user,
			self::COLUMN_TIME		=>	date('c', $time),
			self::COLUMN_DESK		=> $table_id
		));
	}

	/** @return Nette\Database\Table\Selection */
	public function getReservedTables($timestamp) {
		if (empty($timestamp)) {
			return;
		}
		// vrat obsazene stolu v danou dobu
		return $this->database->table(self::TABLE_BOOKING)
				->where(self::COLUMN_TIME . ' BETWEEN ? AND ?',
				date('c', strtotime('-2 hours', $timestamp)), 
				date('c', strtotime('+2 hours', $timestamp)));
	}
	
	/** @return Nette\Database\Table\Selection */
	public function getAllTables() {
		return $this->database->table(self::TABLE_TABLE)
				->order(self::COLUMN_ID);
	}
	
	/** @return Nette\Database\Table\Selection */
	public function getFollowingBookings($user) {
		if (empty($user)) {
			return;
		}
		return $this->database->table(self::TABLE_BOOKING)
				->where(self::COLUMN_TIME . " > NOW()")
				->where(self::COLUMN_CUSTOMER, $user);
	}
	
	/** @return Nette\Database\Table\Selection */
	public function getPreviousBookings($user) {
		if (empty($user)) {
			return;
		}
		return $this->database->table(self::TABLE_BOOKING)
				->where(self::COLUMN_TIME . " < NOW() + INTERVAL '2 hours'")
				->where(self::COLUMN_CUSTOMER, $user);
	}
	
	/* @return Nette\Database\Table\ActiveRow */
	public function get($id) {
		if (empty($id)) {
			return;
		}
		return $this->database->table(self::TABLE_BOOKING)
				->where(self::COLUMN_ID, $id)
				->fetch();
	}
	
	/** @return int Number of deleted bookings */
	public function cancel($id, $user) {
		if (empty($id) || empty($user)) {
			return;
		}
		return $this->database->table(self::TABLE_BOOKING)
				->where(self::COLUMN_ID, $id)
				->where(self::COLUMN_CUSTOMER, $user)
				->where(self::COLUMN_TIME . ' > NOW()')
				->delete();
	}

}