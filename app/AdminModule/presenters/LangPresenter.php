<?php

namespace App\AdminModule\Presenters;

use App\Enum\UserRoleEnum;
use App\Forms\LangForm;
use App\Forms\LangItemForm;
use App\Model\LangRepository;
use App\Model\WebconfigRepository;

class LangPresenter extends SignPresenter {

	/** @var LangForm $langForm */
	private $langForm;

	/** @var LangItemForm */
	private $langItemForm;

	/** @var LangRepository */
	private $langRepository;

	/** @var WebconfigRepository */
	private $webconfigRepository;

	public function __construct(LangForm $langForm, LangItemForm $langItemForm, LangRepository $langRepository, WebconfigRepository $webconfigRepository) {
		$this->langForm = $langForm;
		$this->langItemForm = $langItemForm;
		$this->langRepository = $langRepository;
		$this->webconfigRepository = $webconfigRepository;
	}

	public function startup() {
		parent::startup();

		$userRole = $this->getUser()->getRoles();
		$adminRole = UserRoleEnum::USER_ROLE_ADMINISTRATOR;
		$userRole = reset($userRole);
		if ($userRole != $adminRole) {
			$this->flashMessage(USER_REQUEST_NOT_PRIV, "alert-danger");
			$this->redirect("Dashboard:Default");
		}
	}

	public function renderDefault() {
		$defaults = $this->webconfigRepository->load(WebconfigRepository::KEY_LANG_FOR_COMMON);
		/*foreach ($defaultsCommon as $key => $value) {
			$defaults[$key] = $value;
		} */
		$this['langForm']->setDefaults($defaults);

		$this->template->langFlagKey = LangRepository::KEY_LANG_ITEM_FLAG;
		$this->template->langMutations = $this->langRepository->findLanguagesWithFlags();
	}

	public function createComponentLangForm() {
		$form = $this->langForm->create();
		$form->onSuccess[] = $this->saveLangCommon;

		return $form;
	}

	public function saveLangCommon($form, $values) {
		foreach ($values as $key => $value) {
			$this->webconfigRepository->save($key, $value, WebconfigRepository::KEY_LANG_FOR_COMMON);
		}
	}

	public function createComponentLangItemForm() {
		$form = $this->langItemForm->create();
		$form->onSuccess[] = $this->saveLangItem;

		return $form;
	}

	public function saveLangItem() {

	}

	public function actionDelete($id) {
		$this->redirect('default');
	}
}