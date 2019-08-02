<?php

namespace App\AdminModule\Presenters;

use App\Controller\MenuController;
use App\Enum\UserRoleEnum;
use App\Forms\MenuForm;
use App\Model\Entity\MenuEntity;
use App\Model\LangRepository;
use App\Model\MenuRepository;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;

class MenuPresenter extends SignPresenter {

	/** @var MenuPresenter  */
	private $menuForm;

	public function __construct(
		MenuForm $menuForm
	) {
		$this->menuForm = $menuForm;
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

	public function actionDefault() {
		$lang = $this->langRepository->getCurrentLang($this->session);
		$this->template->topMenuEntities = $this->menuRepository->findItems($lang);
		$this->template->menuController = $this->menuController;
		$this->template->presenter = $this->presenter;
	}

	public function createComponentMenuForm() {
		$form = $this->menuForm->create($this->langRepository->findLanguages());
		$form->onSuccess[] = [$this, 'saveMenuItem'];
		return $form;
	}

	/**
	 * @param int $id
	 */
	public function actionDelete($id) {
		$this->menuRepository->delete($id);
		$this->flashMessage(MENU_SETTINGS_ITEM_DELETED, "alert-success");
		$this->redirect("default");
	}

	/**
	 * @param Form $form
	 * @param ArrayHash $values
	 */
	public function saveMenuItem($form, $values) {
		$level = (isset($values['level']) ? $values['level'] : 1);
		$submenu = (isset($values['submenu']) ? $values['submenu'] : 0);

		$langItems = [];
		foreach ($values as $item) {
			if ($item instanceof ArrayHash) {
				$menuEntity = new MenuEntity();
				$menuEntity->hydrate((array)$item);
				$menuEntity->setSubmenu($submenu);
				$menuEntity->setLevel($level);
				if ($this->menuRepository->getMenuItemByLink($menuEntity->getLink(), $menuEntity->getLang()) != null) {
					$message = sprintf(MENU_SETTINGS_ITEM_LINK_EXISTS, $menuEntity->getLink());
					$this->flashMessage($message, "alert-danger");
					$this->redirect("edit", null, $values);
				}
				$langItems[] = $menuEntity;
			}
		}

		if ($this->menuRepository->saveItem($langItems)) {
			$this->flashMessage(MENU_SETTINGS_ITEM_LINK_ADDED, "alert-success");
			$this->redirect("default");
		} else {
			$this->flashMessage(MENU_SETTINGS_ITEM_LINK_FAILED, "alert-danger");
			$this->redirect("edit", null, $values);
		}
	}

	/**
	 * @param int $id
	 */
	public function actionMoveUp($id) {
		if ($this->menuRepository->orderEntryUp($id)) {
			$this->flashMessage(MENU_SETTINGS_ITEM_MOVE_UP, "alert-success");
		} else {
			$this->flashMessage(MENU_SETTINGS_ITEM_MOVE_FAILED, "alert-danger");
		}
		$this->redirect("default");
	}

	/**
	 * @param int $id
	 */
	public function actionMoveDown($id) {
		if ($this->menuRepository->orderEntryDown($id)) {
			$this->flashMessage(MENU_SETTINGS_ITEM_MOVE_DOWN, "alert-success");
		} else {
			$this->flashMessage(MENU_SETTINGS_ITEM_MOVE_FAILED, "alert-danger");
		}
		$this->redirect("default");
	}

	/**
	 * @param $id
	 */
	public function actionEdit($id, array $values = null, $level = null) {
		if (!empty($values)) {	// edit mode when error during saving
			$this['menuForm']->setDefaults($values);
		}

		if ($id != null && $level == null) {	// classic edit mode
			$values = $this->menuController->prepareMenuItemsForEdit($id);
			$this['menuForm']->setDefaults($values);
		}

		if ($level != null) {
			$this['menuForm']['level']->setValue($level);
			$this['menuForm']['submenu']->setValue($id);
		}
	}
}