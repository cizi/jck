<?php

namespace App\AdminModule\Presenters;

use App\AdminModule\Presenters;

class DashboardPresenter extends SignPresenter {

	public function actionDefault($id) {
		$this->template->emails = $this->articleRepository->findEmailFromView();
	}
}