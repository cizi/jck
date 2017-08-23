<?php

namespace App\Enum;

class UserRoleEnum extends Enum {

	public function __construct() {
		parent::__construct(__CLASS__);
	}

	/** @const string */
	const USER_ROLE_LAYOUT_CHANGER = 60;

	/** @const string */
	const USER_ROLE_CONTENT_CHANGER = 30;

	/** @const string */
	const USER_ROLE_GUEST = 10;

	/** @const string */
	const USER_ROLE_ADMINISTRATOR = 99;
}