<?php

namespace App\Model\Entity;

class UserEntity {

	/** @var int */
	private $id;

	/** @var string */
	private $email;

	/** @var string */
	private $password;

	/** @var int */
	private $role;

	/** @var bool */
	private $active;

	/** @var string  */
	private $lastLogin;

	/** @var string  */
	private $registerTimestamp;

	/**
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @param int $id
	 */
	public function setId($id) {
		$this->id = $id;
	}

	/**
	 * @return string
	 */
	public function getLastLogin() {
		return $this->lastLogin;
	}

	/**
	 * @param string $lastLogin
	 */
	public function setLastLogin($lastLogin) {
		$this->lastLogin = $lastLogin;
	}

	/**
	 * @return string
	 */
	public function getEmail() {
		return $this->email;
	}

	/**
	 * @param string $login
	 */
	public function setEmail($email) {
		$this->login = $email;
	}

	/**
	 * @return string
	 */
	public function getPassword() {
		return $this->password;
	}

	/**
	 * @param string $password
	 */
	public function setPassword($password) {
		$this->password = $password;
	}

	/**
	 * @return int
	 */
	public function getRole() {
		return $this->role;
	}

	/**
	 * @param int $role
	 */
	public function setRole($role) {
		$this->role = $role;
	}

	/**
	 * @return boolean
	 */
	public function isActive() {
		return $this->active;
	}

	/**
	 * @param boolean $active
	 */
	public function setActive($active) {
		$this->active = $active;
	}

	/**
	 * @return string
	 */
	public function getRegisterTimestamp() {
		return $this->registerTimestamp;
	}

	/**
	 * @param string $registerTimestamp
	 */
	public function setRegisterTimestamp($registerTimestamp) {
		$this->registerTimestamp = $registerTimestamp;
	}

	/**
	 * @param array $data
	 */
	public function hydrate(array $data) {
		$this->id = (isset($data['id']) ? $data['id'] : null);
		$this->email = (isset($data['email']) ? $data['email']: null);
		$this->password = (isset($data['password']) ? $data['password'] : null);
		$this->role = (isset($data['role']) ? $data['role'] : null);
		$this->active = (isset($data['active']) ? $data['active'] : null);
		$this->lastLogin = (isset($data['last_login']) ? $data['last_login'] : null);
		$this->registerTimestamp = (isset($data['register_timestamp']) ? $data['register_timestamp'] : null);
	}

	/**
	 * @return array
	 */
	public function extract() {
		return [
			'id' => $this->id,
			'email' => $this->email,
			'password' => $this->password,
			'role' => $this->role,
			'active' => $this->active,
			'last_login' => $this->lastLogin,
			'register_timestamp' => $this->registerTimestamp
		];
	}
}