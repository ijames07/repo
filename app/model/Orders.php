<?php

namespace App\Model;

use Nette;

class Orders extends \Nette\Object {
	/** @var Nette\Database\Context */
	private $database;
	
	const
			TABLE_NAME = 'order',
			COLUMN_ID = 'id',
			COLUMN_CUSTOMER = 'customer_id',
			COLUMN_PRODUCT = 'product_id',
			COLUMN_EMPLOYEE = 'employee_id',
			COLUMN_PICKUP = 'pickup_time',
			COLUMN_SOLVED = 'solved',
			COLUMN_PREPARED = 'prepared',
			COLUMN_CREATION= 'creation_date';
	
	public function __construct(Nette\Database\Context $database) {
		$this->database = $database;
	}
	
	/** @return Nette\Database\Table\ActiveRow */
	public function insertOrder($cust_id, $prod_id, $pickup) {
		return $this->database->table(self::TABLE_NAME)->insert(array(
			self::COLUMN_CUSTOMER	=> $cust_id,
			self::COLUMN_PRODUCT	=> intval($prod_id),
			self::COLUMN_PICKUP		=> date('c', strtotime($pickup))
		));
	}
	
	/** @return Nette\Database\Table\Selection */
	public function getAll(Nette\Utils\Paginator $paginator = null) {
		if ($paginator == null) {
			return $this->database->table(self::TABLE_NAME)
					->order(self::COLUMN_PICKUP . ' DESC');
		} else {
			return $this->database->table(self::TABLE_NAME)
					->order(self::COLUMN_PICKUP . ' DESC')
					->limit($paginator->getItemsPerPage(), $paginator->getOffset());	
		}

	}
	
	/** @return Nette\Database\Table\ActiveRow */
	public function getOrder($id = 0) {
		return $this->database->table(self::TABLE_NAME)->get($id);
	}
	
	/** @return Nette\Database\Table\Selection All successfully picked orders */
	public function getPicked($id) {
		if (empty($id)) {
			return;
		}
		return $this->database->table(self::TABLE_NAME)
				->where(self::COLUMN_CUSTOMER, $id)
				->where(self::COLUMN_SOLVED . ' IS NOT NULL')
				->where(self::COLUMN_EMPLOYEE . ' IS NOT NULL');
	}
	
	/** @return Nette\Database\Table\Selection All opened orders */
	public function getOpened($id) {
		if (empty($id)) {
			return;
		}
		return $this->database->table(self::TABLE_NAME)
				->where(self::COLUMN_CUSTOMER, $id)
				->where(self::COLUMN_SOLVED . ' IS NULL')
				->where(self::COLUMN_PICKUP . ' > NOW() - INTERVAL 8 HOUR')
				->where(self::COLUMN_EMPLOYEE . ' IS NULL');
	}
	
	/** @return int */
	public function updateOrder($values) {
		if (empty($values)) {
			return;
		}
		return $this->database->table(self::TABLE_NAME)
				->where(self::COLUMN_ID, $values["order_id"])
				->update(array(
					self::COLUMN_SOLVED	=> date('c', $values["solved"])
				));
	}

	/** @return Nette\Database\Table\Selection All unpicked orders */
	public function getUnpicked($id) {
		if (empty($id)) {
			return;
		}
		return $this->database->table(self::TABLE_NAME)
				->where(self::COLUMN_CUSTOMER, $id)
				->where(self::COLUMN_SOLVED . ' IS NULL')
				->where(self::COLUMN_EMPLOYEE . ' IS NOT NULL')
				->where("NOW() > " . self::COLUMN_PICKUP . " + INTERVAL 2 HOUR");
	}
	
	/** @return Nette\Database\Table\Selection All currently prepared orders */
	public function getPrepared($id) {
		if (empty($id)) {
			return;
		}
		return $this->database->table(self::TABLE_NAME)
				->where(self::COLUMN_CUSTOMER, $id)
				->where(self::COLUMN_SOLVED . ' IS NULL')
				->where(self::COLUMN_EMPLOYEE . ' IS NOT NULL')
				->where("NOW() < " . self::COLUMN_PICKUP . " + INTERVAL 2 HOUR");
	}
	
	/** @return Nette\Database\Table\Selection All cancelled orders */
	public function getCancelled($id) {
		if (empty($id)) {
			return;
		}
		return $this->database->table(self::TABLE_NAME)
				->where(self::COLUMN_CUSTOMER, $id)
				->where(self::COLUMN_SOLVED . ' IS NOT NULL')
				->where(self::COLUMN_EMPLOYEE . ' IS NULL')
				->order(self::COLUMN_SOLVED);
	}
	
	/** @return int */
	public function cancel($order, $user) {
		if (empty($order) || empty($user)) {
			return 0;
		}
		return $this->database->table(self::TABLE_NAME)
				->where(self::COLUMN_ID, $order)
				->where(self::COLUMN_CUSTOMER, $user)
				->where(self::COLUMN_EMPLOYEE . ' IS NULL')
				->where(self::COLUMN_SOLVED . ' IS NULL')
				->update(array(
					self::COLUMN_SOLVED => 'NOW()'
				));
	}
	
	/** @return Nette\Database\Table\Selection */
	public function getServed($user) {
		if (empty($user)) {
			return;
		}
		return $this->database->table(self::TABLE_NAME)
				->where(self::COLUMN_EMPLOYEE, $user)
				->order(self::COLUMN_SOLVED);
	}
	
	/** @return Nette\Database\Table\Selection */
	public function getUnprepared($id) {
		if (empty($id)) {
			return;
		} else {
			return $this->database->table(self::TABLE_NAME)
					->where(self::COLUMN_CUSTOMER, $id)
					->where(self::COLUMN_EMPLOYEE . ' IS NULL')
					->where(self::COLUMN_SOLVED . ' IS NULL')
					->where(self::COLUMN_PICKUP . ' < NOW() + INTERVAL 12 HOUR');
		}
	}
	
	/** @return Nette\Database\Table\Selection */
	public function getOrdersForProcessing($ts = '') {
		if ($ts == '') {
			return $this->database->table(self::TABLE_NAME)
					->where(self::COLUMN_SOLVED . ' IS NULL')
					->where(self::COLUMN_CREATION . " >= (NOW() - INTERVAL 8 HOUR)")
					->where('customer.blocked = ?', false)
					->order(self::COLUMN_PICKUP . ' ASC');
		} else {
			$date = date('c', $ts);
			return $this->database->table(self::TABLE_NAME)
					->where(self::COLUMN_SOLVED . ' IS NULL')
					->where('customer.blocked = ?', false)
					->where(self::COLUMN_CREATION . " >= ? OR " . self::COLUMN_PREPARED . " >= ?", $date, $date)
					->order(self::COLUMN_PICKUP . ' ASC');
		}
	}
	
	/** @return int */
	public function prepare($id, $emp) {
		if (empty($id)) {
			return;
		}
		return $this->database->table(self::TABLE_NAME)
				->where(self::COLUMN_ID, intval($id))
				->where(self::COLUMN_EMPLOYEE . ' IS NULL')
				->update(array(
					self::COLUMN_EMPLOYEE => $emp,
					self::COLUMN_PREPARED => 'NOW()'
				));
	}
	
	/** @return int */
	public function finish($id) {
		if (empty($id)) {
			return;
		}
		return $this->database->table(self::TABLE_NAME)
				->where(self::COLUMN_ID, intval($id))
				->where(self::COLUMN_EMPLOYEE . ' IS NOT NULL')
				->update(array(
					self::COLUMN_SOLVED => 'NOW()'
				));
	}
	
	/** @return Nette\Database\ResultSet */
	public function getMostOrderedProduct($user) {
		if (empty($user)) {
			// nejcasteji objednavane produkty
			return $this->database->query('	SELECT `product`.`name`, COUNT(*) AS `pocet` FROM `order`
											JOIN `product` ON (`order`.`product_id` = `product`.`id`)
											GROUP BY `product`.`name`
											ORDER BY `pocet` DESC');
		} else {
			// 3 nejcasteji objednavane produkty uzivatele
			return $this->database->query('	SELECT `product`.`name`, COUNT(*) AS `pocet` FROM `order`
											JOIN `product` ON (`order`.`product_id` = `product`.`id`)
											WHERE `customer_id` = ? 
											GROUP BY `product`.`name`
											ORDER BY `pocet` DESC
											LIMIT 3', $user);
		}
	}
	
	/** @return Nette\Database\ResultSet */
	public function getMostOrderedCategory($user) {
		if (empty($user)) {
			// Nejcasteji objednavane kategorie
			return $this->database->query('	SELECT `category`.`name`, COUNT(*) AS `pocet` FROM `order`
											JOIN `product` ON (`order`.`product_id` = `product`.`id`)
											JOIN `category_product` ON (`product`.`id` = `category_product`.`product_id`)
											JOIN `category` ON (`category_product`.`category_id` = `category`.`id`)
											GROUP BY `category`.`name`
											ORDER BY `pocet` DESC');
		} else {
			// 3 nejcasteji objednavane kategorie uzivatele
			return $this->database->query('	SELECT `category`.`name`, COUNT(*) AS `pocet` FROM `order`
											JOIN `product` ON (`order`.`product_id` = `product`.`id`)
											JOIN `category_product` ON (`product`.`id` = `category_product`.`product_id`)
											JOIN `category` ON (`category_product`.`category_id` = `category`.`id`)
											WHERE `customer_id` = ? 
											GROUP BY `category`.`name`
											ORDER BY `pocet` DESC
											LIMIT 3', $user);
			/** SELECT `category`.`name`, COUNT(DISTINCT `order`.`id`) AS `pocet` FROM `order`
JOIN `product` ON (`order`.`product_id` = `product`.`id`)
JOIN `category_product` ON (`product`.`id` = `category_product`.`product_id`)
JOIN `category` ON (`category_product`.`category_id` = `category`.`id`)
WHERE `customer_id` = 3 AND `order`.`product_id`= `product`.`id`
ORDER BY `pocet` DESC**/
		}
	}
}