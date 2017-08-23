<?php

namespace App\AdminModule\Presenters;

use Nette;
use App\FrontendModule\Presenters\BasePresenter;

class SignPresenter extends BasePresenter {

	/**
	 * If the user is not logged in is necessary to log him in
	 */
	public function startup() {
		if($this->getUser()->isLoggedIn() == false){
			$this->redirect('Default:default');
		}
		parent::startup();
	}
}
