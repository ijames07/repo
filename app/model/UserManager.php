<?php

namespace App\Model;

use Nette,
	Nette\Security\Passwords;

/**
 * Users management.
 */
class UserManager extends Nette\Object implements Nette\Security\IAuthenticator {

	const
			TABLE_NAME = 'customer',
			COLUMN_ID = 'id',
			COLUMN_LOGIN = 'email',
			COLUMN_PASSWORD = 'password',
			COLUMN_NAME = "name",
			COLUMN_SURNAME = "surname",
			COLUMN_NEWSLETTER = "newsletter",
			COLUMN_ACTIVE = "active",
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
	 * @return void
	 */
	public function add($data) {
		$this->database->table(self::TABLE_NAME)->insert(array(
			self::COLUMN_LOGIN => $data['email'],
			self::COLUMN_PASSWORD => Passwords::hash($data["password"]),
			self::COLUMN_GENDER => (boolean) $data['gender'],
			self::COLUMN_NAME => $data['name'],
			self::COLUMN_SURNAME => $data['surname'],
			self::COLUMN_NEWSLETTER => $data['newsletter']
		));
	}
	
}
