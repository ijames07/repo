<?php

namespace App\Model;

use Nette,
	Nette\Security\Passwords;

/**
 * Users management.
 */
class UserManager extends Nette\Object implements Nette\Security\IAuthenticator {

	const
			TABLE_NAME = 'user',
			COLUMN_ID = 'id',
			COLUMN_LOGIN = 'email',
			COLUMN_PASSWORD = 'password',
			COLUMN_NAME = "name",
			COLUMN_SURNAME = "surname",
			COLUMN_NEWSLETTER = "newsletter",
			COLUMN_ACTIVE = "active",
			COLUMN_BLOCKED = 'blocked',
			COLUMN_GENDER = "gender",
			COLUMN_ROLE = 'role';

	/** @var Nette\Database\Context */
	private $database;

	public function __construct(Nette\Database\Context $database) {
		$this->database = $database;
	}

	/**
	 * Performs an authentication.
	 * @return Nette\Security\Identity
	 * @throws Nette\Security\AuthenticationException
	 */
	public function authenticate(array $credentials) {
		list($username, $password) = $credentials;

		$row = $this->database->table(self::TABLE_NAME)->where(self::COLUMN_LOGIN, $username)->fetch();

		if (!$row) {
			throw new Nette\Security\AuthenticationException('The username is incorrect.', self::IDENTITY_NOT_FOUND);

		} elseif (!Passwords::verify($password, $row[self::COLUMN_PASSWORD])) {
			throw new Nette\Security\AuthenticationException('The password is incorrect.', self::INVALID_CREDENTIAL);

		} elseif (Passwords::needsRehash($row[self::COLUMN_PASSWORD])) {
			$row->update(array(
				self::COLUMN_PASSWORD => Passwords::hash($password),
			));
		}

		$arr = $row->toArray();
		unset($arr[self::COLUMN_PASSWORD]);
		return new Nette\Security\Identity($row[self::COLUMN_ID], $row[self::COLUMN_ROLE], $arr);
	}

	/**
	 * Adds new user.
	 * @param  string
	 * @return Nette\Database\Table\ActiveRow 
	 */
	public function add($data) {
		try {
			return $this->database->table(self::TABLE_NAME)->insert(array(
				self::COLUMN_LOGIN => $data['email'],
				self::COLUMN_PASSWORD => Passwords::hash($data["password"]),
				self::COLUMN_GENDER => (boolean) $data['gender'],
				self::COLUMN_NAME => $data['name'],
				self::COLUMN_SURNAME => $data['surname'],
				self::COLUMN_NEWSLETTER => $data['newsletter']
			));
		} catch (\PDOException $e) {
			if ($e->getCode() == 23505) {
				return -1;
			} else {
				return false;
			}
		}
	}
	
	/** @return int */
	public function activate($email) {
		return $this->database->table(self::TABLE_NAME)
				->where(self::COLUMN_LOGIN, $email)
				->update(array(
					self::COLUMN_ACTIVE => true
				));
	}
	
	/** @return Nette\Database\Table\ActiveRow */
	public function get($id = '') {
		if ($id == '') {
			return;
		}
		return $this->database->table(self::TABLE_NAME)->get($id);
	}
	
	/** @return int Number of all users in DB */
	public function usersCount() {
		return $this->database->table(self::TABLE_NAME)->count();
	}
	
	/** @return Nette\Database\Table\Selection */
	public function getAll() {
		return $this->database->table(self::TABLE_NAME)->order(self::COLUMN_SURNAME);
	}
	
	/** @return int */
	public function changePermission($data) {
		if (empty($data)) {
			return;
		}
		return $this->database->table(self::TABLE_NAME)
				->where(self::COLUMN_ID, intval($data->employee))
				->update(array(
					self::COLUMN_ROLE => $data->role == 1 ? 'manager' : ($data->role == 0? 'employee' : 'customer')
				));
	}
	
	/** @return Nette\Database\Table\Selection */
	public function userLetter($letter) {
		if (empty($letter)) {
			return;
		}
		$letter = substr($letter, 0, 2);
		return $this->database->table(self::TABLE_NAME)
				->where('LOWER(' . self::COLUMN_SURNAME . ") LIKE LOWER('" . $letter . "%')");
	}
	
	/** @return int */
	public function toggleBlocked($id) {
		if (empty($id)) {
			return;
		}
		$user = $this->database->table(self::TABLE_NAME)
				->get($id);
		if (empty($user)) {
			return;
		}
		return $this->database->table(self::TABLE_NAME)
				->where(self::COLUMN_ID, $id)
				->update(array(
					self::COLUMN_BLOCKED => $user->blocked ? false : true
				));
	}
	
	/** @return Nette\Database\ResultSet */
	public function overallManagerInfo(Nette\Utils\Paginator $paginator, $letter = '') {
		if ($letter != '') {
			return $this->database->query('	SELECT `email`, `blocked`, `name`, `surname`, `user`.`id`, `gender`, `registered`,
								SUM(CASE WHEN `solved` IS NOT NULL AND `employee_id` IS NOT NULL THEN 1 ELSE 0 END) AS `picked`,
								SUM(CASE WHEN `solved` IS NULL AND `employee_id` IS NULL AND `order`.id IS NOT NULL THEN 1 ELSE 0 END) AS `opened`,
								SUM(CASE WHEN `solved` IS NOT NULL AND `employee_id` IS NULL THEN 1 ELSE 0 END) AS cancelled,
								SUM(CASE WHEN `solved` IS NULL AND NOW() > (`pickup_time` + INTERVAL 2 HOUR) AND `employee_id` IS NOT NULL THEN 1 ELSE 0 END) AS `left`
								FROM `user`
								LEFT JOIN `order` ON (`user`.`id` = `order`.`customer_id`)
								WHERE LOWER(`surname`) LIKE ' . " LOWER('" . $letter . "%') " . '
								GROUP BY `email`, `name`, `surname`, `user`.`id`, `gender`
								ORDER BY `surname`, `name` LIMIT ? OFFSET ?', $paginator->getLength(), $paginator->getOffset());
		} else {
			return $this->database->query('	SELECT `email`, `blocked`, `name`, `surname`, `user`.`id`, `gender`, `registered`,
											SUM(CASE WHEN `solved` IS NOT NULL AND `employee_id` IS NOT NULL THEN 1 ELSE 0 END) AS `picked`,
											SUM(CASE WHEN `solved` IS NULL AND `employee_id` IS NULL AND `order`.id IS NOT NULL THEN 1 ELSE 0 END) AS `opened`,
											SUM(CASE WHEN `solved` IS NOT NULL AND `employee_id` IS NULL THEN 1 ELSE 0 END) AS `cancelled`,
											SUM(CASE WHEN `solved` IS NULL AND NOW() > (`pickup_time` + INTERVAL 2 HOUR) AND `employee_id` IS NOT NULL THEN 1 ELSE 0 END) AS `left`
											FROM `user`
											LEFT JOIN `order` ON (`user`.`id` = `order`.`customer_id`)
											GROUP BY `email`, `name`, `surname`, `user`.`id`, `gender`
											ORDER BY `surname`, `name` LIMIT ? OFFSET ?', $paginator->getLength(), $paginator->getOffset());
		}
	}
	
	/** @return Nette\Database\ResultSet */
	public function overallEmployeeInfo(Nette\Utils\Paginator $paginator, $letter = '') {
		if ($letter != '') {
			$letter = substr($letter, 0, 2);
			return $this->database->query('	SELECT `email`, `blocked`, `name`, `surname`, `user`.`id`, `gender`, `registered`,
								SUM(CASE WHEN `solved` IS NOT NULL AND `employee_id` IS NOT NULL THEN 1 ELSE 0 END) AS `picked`,
								SUM(CASE WHEN `solved` IS NULL AND `employee_id` IS NULL AND `order`.id IS NOT NULL THEN 1 ELSE 0 END) AS `opened`,
								SUM(CASE WHEN `solved` IS NOT NULL AND `employee_id` IS NULL THEN 1 ELSE 0 END) AS `cancelled`,
								SUM(CASE WHEN `solved` IS NULL AND NOW() > (`pickup_time` + INTERVAL 2 HOUR) AND `employee_id` IS NOT NULL THEN 1 ELSE 0 END) AS `left`
								FROM `user`
								LEFT JOIN `order` ON (`user`.`id` = `order`.`customer_id`)
								WHERE LOWER(`surname`) LIKE' .  " LOWER('" . $letter . "%') " .  'AND role = \'customer\'
								GROUP BY `email`, `name`, `surname`, `user`.`id`, `gender`
								ORDER BY `surname`, `name` LIMIT ? OFFSET ?', $paginator->getLength(), $paginator->getOffset());
		} else {
			return $this->database->query('	SELECT `email`, `blocked`, `name`, `surname`, `user`.`id`, `gender`, `registered`,
								SUM(CASE WHEN `solved` IS NOT NULL AND `employee_id` IS NOT NULL THEN 1 ELSE 0 END) AS `picked`,
								SUM(CASE WHEN `solved` IS NULL AND `employee_id` IS NULL AND `order`.id IS NOT NULL THEN 1 ELSE 0 END) AS `opened`,
								SUM(CASE WHEN `solved` IS NOT NULL AND `employee_id` IS NULL THEN 1 ELSE 0 END) AS `cancelled`,
								SUM(CASE WHEN `solved` IS NULL AND NOW() > (`pickup_time` + INTERVAL 2 HOUR) AND `employee_id` IS NOT NULL THEN 1 ELSE 0 END) AS `left`
								FROM `user`
								LEFT JOIN `order` ON (`user`.`id` = `order`.`customer_id`)
								WHERE role = \'customer\'
								GROUP BY `email`, `name`, `surname`, `user`.id, `gender`
								ORDER BY `surname`, `name` LIMIT ? OFFSET ?', $paginator->getLength(), $paginator->getOffset());
		}
	}
}

class DuplicateNameException extends \Exception {
	
}