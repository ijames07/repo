<?php

namespace App\Model;

use Nette;

class Bookings extends \Nette\Object {
	/** @var Nette\Database\Context */
	private $database;
	
	const TABLE_BOOKING = 'booking',
			TABLE_DESK = 'desk',
			COLUMN_ID = 'id',
			COLUMN_TIME = 'time',
			COLUMN_DESK = 'desk_id',
			COLUMN_FINISHED = 'finished',
			COLUMN_CUSTOMER = 'customer_id';
	
	public function __construct(Nette\Database\Context $database) {
		$this->database = $database;
	}
	
	/** @return Nette\Database\ResultSet */
	public function getPopularDesks() {
		return $this->database->query('	SELECT `desk`.`id`, `desk`.`seats`, `desk`.`smoking`, `desk`.`indoor`, count(*) AS rezervovano FROM `booking`
										JOIN `desk` ON (`desk`.`id` = `booking`.`desk_id`)
										GROUP BY `desk`.`id` ORDER BY `rezervovano` DESC
										LIMIT 10');
	}
	
	public function getPopularDeskType() {
		return $this->database->query('	SELECT `desk`.`smoking`, count(*) AS pocet FROM `booking`
										JOIN `desk` ON (`desk`.`id` = `booking`.`desk_id`)
										GROUP BY `desk`.`smoking` ORDER BY `pocet` DESC');
	}
	
	/** @return Nette\Database\Table\ActiveRow */
	public function add($user, $table_id, $time) {
		if (empty($user) || empty($table_id) || empty($time)) {
			return;
		}
		$this->database->table(self::TABLE_BOOKING)->insert(array(
			self::COLUMN_CUSTOMER	=>	$user,
			self::COLUMN_TIME		=>	date('c', floor($time/3600) * 3600),
			self::COLUMN_DESK		=>	$table_id
		));
	}
	
	/** @retun Nette\Database\Table\Selection */
	public function getTodaysBookings() {
		return $this->database->table(self::TABLE_BOOKING)
				->where(self::COLUMN_TIME . ' BETWEEN CURDATE() AND CURDATE() + INTERVAL 1 DAY')
				->where(self::COLUMN_FINISHED, false)
				->order(self::COLUMN_TIME . ' ASC');
	}	
	
	/** @return int */
	public function finish($id) {
		if (empty($id)) {
			return;
		}
		return $this->database->table(self::TABLE_BOOKING)
				->where(self::COLUMN_ID, $id)
				->update(array(
					self::COLUMN_FINISHED => true
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
				date('c', strtotime('+4 hours', $timestamp)));
	}
	
	/** @return Nette\Database\Table\Selection */
	public function getFollowingBookings($user) {
		if (empty($user)) {
			return;
		}
		return $this->database->table(self::TABLE_BOOKING)
				->where(self::COLUMN_TIME . " > NOW()")
				->where(self::COLUMN_CUSTOMER, $user)
				->order(self::COLUMN_TIME . ' ASC');
	}
	
	/** @return Nette\Database\Table\Selection */
	public function getPreviousBookings($user) {
		if (empty($user)) {
			return;
		}
		return $this->database->table(self::TABLE_BOOKING)
				->where("NOW() + INTERVAL 2 HOUR > " . self::COLUMN_TIME)
				->where(self::COLUMN_CUSTOMER, $user)
				->order(self::COLUMN_TIME . ' DESC');
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
	public function cancel($id, $user = '') {
		if (empty($id)) {
			return;
		}
		if ($user != '') {
			return $this->database->table(self::TABLE_BOOKING)
					->where(self::COLUMN_ID, $id)
					->where(self::COLUMN_CUSTOMER, $user)
					->where(self::COLUMN_TIME . ' > NOW()')
					->delete();
		} else {
			// manager deletes
			return $this->database->table(self::TABLE_BOOKING)
					->where(self::COLUMN_ID, $id)
					->where(self::COLUMN_TIME . ' > NOW()')
					->delete();
		}
	}
	
	/** @return Nette\Database\Table\Selection */
	public function getAll(Nette\Utils\Paginator $paginator = null) {
		if ($paginator == null) {
			return $this->database->table(self::TABLE_BOOKING)
					->order(self::COLUMN_TIME . ' DESC');
		} else {
			return $this->database->table(self::TABLE_BOOKING)
					->order(self::COLUMN_TIME . ' DESC')
					->limit($paginator->getItemsPerPage(), $paginator->getOffset());
		}
	}

}