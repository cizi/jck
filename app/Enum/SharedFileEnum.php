<?php

namespace App\Enum;

class SharedFileEnum extends Enum {

	public function __construct() {
		parent::__construct(__CLASS__);
	}

	/** @const int defaultní hodnota  = sdílené obrázky */
	const PIC = 0;

	/** @const int = dokumenty */
	const DOC= 1;

}