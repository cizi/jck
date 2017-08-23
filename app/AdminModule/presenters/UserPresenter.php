<?php

namespace App\AdminModule\Presenters;

use App\AdminModule\Model;
use App\Enum\UserRoleEnum;
use App\Forms\UserFilterForm;
use App\Forms\UserForm;
use App\Model\Entity\UserEntity;
use App\Model\UserRepository;
use Nette\Forms\Form;
use Nette\Security\Passwords;
use Nette\Security\User;
use Nette\Utils\Paginator;

class UserPresenter extends SignPresenter {

	/** @var UserRepository */
	private $userRepository;

	/** @var UserForm */
	private $userForm;

	/** @var UserFilterForm  */
	private $userFilterForm;

	/**
	 * UserPresenter constructor.
	 * @param UserRepository $userRepository
	 * @param UserForm $userForm
	 * @param UserFilterForm $userFilterForm
	 */
	public function __construct(UserRepository $userRepository, UserForm $userForm, UserFilterForm $userFilterForm) {
		$this->userRepository = $userRepository;
		$this->userForm = $userForm;
		$this->userFilterForm = $userFilterForm;
	}

	/**
	 * @param int $id
	 * @param string $filter
	 */
	public function actionDefault($id, $filter) {
		$page = (empty($id) ? 1 : intval($id));
		$this['userFilterForm'][UserRepository::USER_CURRENT_PAGE]->setDefaultValue($page);
		$paginator = new Paginator();
		$paginator->setItemCount($this->userRepository->getUsersCount($filter)); // celkový počet položek
		$paginator->setItemsPerPage(50); // počet položek na stránce
		$paginator->setPage($page); // číslo aktuální stránky, číslováno od 1

		$userRoles = new UserRoleEnum();
		$this->template->paginator = $paginator;
		$this->template->users = $this->userRepository->findUsers($paginator, $filter);
		$this->template->roles = $userRoles->translatedForSelect();
	}

	/**
	 * @param int $id
	 */
	public function actionDeleteUser($id) {
		if ($this->userRepository->deleteUser($id)) {
			$this->flashMessage(USER_DELETED, "alert-success");
		} else {
			$this->flashMessage(USER_DELETED_FAILED, "alert-danger");
		}
		$this->redirect('default');
	}

	public function createComponentEditForm() {
		$form = $this->userForm->create($this);
		$form->onSuccess[] = $this->saveUser;

		return $form;
	}

	public function saveUser($form, $values) {
		$userEntity = new UserEntity();
		$userEntity->hydrate((array)$values);
		$userEntity->setPassword(Passwords::hash($userEntity->getPassword()));

		try {
			$this->userRepository->saveUser($userEntity);
			if (isset($values['id']) && $values['id'] != "") {
				$this->flashMessage(USER_EDITED, "alert-success");
			} else {
				$this->flashMessage(USER_ADDED, "alert-success");
			}
		} catch (\Exception $e) {
			$this->flashMessage(USER_EDIT_SAVE_FAILED, "alert-danger");
		}
		$this->redirect("Default");
	}

	/**
	 * @param int $id
	 */
	public function actionEdit($id) {
		$this->template->user = null;
		$userEntity = $this->userRepository->getUser($id);
		$this->template->user = $userEntity;

		if ($userEntity) {
			$this['editForm']->addHidden('id', $userEntity->getId());
			$this['editForm']['email']->setAttribute("readonly", "readonly");
			$this['editForm']->setDefaults($userEntity->extract());
		}
	}

	/**
	 *
	 */
	public function handleActiveSwitch() {
		$data = $this->request->getParameters();
		$userId = $data['idUser'];
		$switchTo = (!empty($data['to']) && $data['to'] == "false" ? false : true);

		if ($switchTo) {
			$this->userRepository->setUserActive($userId);
		} else {
			$this->userRepository->setUserInactive($userId);
		}

		$this->terminate();
	}

	public function createComponentUserFilterForm() {
		$form = $this->userFilterForm->create();
		$form->onSubmit[] = $this->submitUserFilterForm;

		$renderer = $form->getRenderer();
		$renderer->wrappers['controls']['container'] = NULL;
		$renderer->wrappers['pair']['container'] = 'div class=form-group';
		$renderer->wrappers['pair']['.error'] = 'has-error';
		$renderer->wrappers['control']['container'] = 'div class=col-md-4';
		$renderer->wrappers['label']['container'] = 'div class="col-md-4 control-label"';
		$renderer->wrappers['control']['description'] = 'span class=help-block';
		$renderer->wrappers['control']['errorcontainer'] = 'span class=help-block';

		return $form;
	}

	public function submitUserFilterForm(Form $form) {
		$array = $form->getHttpData();
		//$currentPage = (isset($array[UserRepository::USER_CURRENT_PAGE]) ? intval($array[UserRepository::USER_CURRENT_PAGE]) : 1);
		if (isset($array[UserRepository::USER_SEARCH_FIELD]) && (trim($array[UserRepository::USER_SEARCH_FIELD])) != "") {
			$this->redirect("default", 1, $array[UserRepository::USER_SEARCH_FIELD]);
		} else {
			$this->redirect("default");
		}
	}
}