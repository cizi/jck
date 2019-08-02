<?php

namespace App\Forms;

use App\Enum\UserRoleEnum;
use Nette;
use Nette\Application\UI\Form;

class UserForm {

    use Nette\SmartObject;

	/** @var FormFactory */
	private $factory;

	/**
	 * @param FormFactory $factory
	 */
	public function __construct(FormFactory $factory) {
		$this->factory = $factory;
	}

	/**
	 * @return Form
	 */
	public function create(Nette\Application\UI\Presenter $presenter) {
		$form = $this->factory->create();
		$form->getElementPrototype()->addAttributes(["onsubmit" => "return requiredFields();"]);

		$i = 0;
		$form->addText("email", USER_EDIT_EMAIL_LABEL)
			->setAttribute("type","email")
			->setAttribute("class", "tinym_required_field form-control")
			->setAttribute("placeholder", USER_EDIT_EMAIL_LABEL)
			->setAttribute("validation", USER_EDIT_EMAIL_VALIDATION)
			->setAttribute("tabindex", $i+1);

		$form->addPassword("password", USER_EDIT_PASS_LABEL)
			->setAttribute("type","password")
			->setAttribute("class", "tinym_required_field form-control")
			->setAttribute("placeholder", USER_EDIT_PASS_LABEL)
			->setAttribute("validation", USER_EDIT_PASS_REQ)
			->setAttribute("tabindex", $i+1);

		$form->addPassword("passwordConfirm", USER_EDIT_PASS_AGAIN_LABEL)
			->setAttribute("type","password")
			->setAttribute("class", "tinym_required_field form-control")
			->setAttribute("placeholder", USER_EDIT_PASS_AGAIN_LABEL)
			->setAttribute("validation", USER_EDIT_PASS_AGAIN_REQ)
			->setAttribute("tabindex", $i+1);

		$form->addText("real_name", ADMIN_LOGIN_REAL_NAME)
			->setAttribute("class", "tinym_required_field form-control")
			->setAttribute("placeholder", ADMIN_LOGIN_REAL_NAME)
			->setAttribute("validation", ADMIN_LOGIN_REAL_NAME_REQ)
			->setAttribute("tabindex", $i+1);

		$form->addText("phone", ADMIN_LOGIN_PHONE)
			->setAttribute("class", "form-control")
			->setAttribute("placeholder", ADMIN_LOGIN_PHONE)
			->setAttribute("tabindex", $i+1);

		$userRole = new UserRoleEnum();
		$form->addSelect("role", USER_EDIT_ROLE_LABEL, $userRole->translatedForSelect())
			->setAttribute("class", "form-control")
			->setAttribute("readonly", "readonly")
			->setDefaultValue(99)
			->setAttribute("tabindex", $i+1);;

		$form->addCheckbox('active')
			->setAttribute("data-toggle", "toggle")
			->setAttribute("data-height", "25")
			->setAttribute("data-width", "50")
			->setDefaultValue("checked")
			->setAttribute("tabindex", $i+1);


		$form->addSubmit("confirm", USER_EDIT_SAVE_BTN_LABEL)
			->setAttribute("class","btn btn-primary")
			->setAttribute("tabindex", $i+1);

		$link = new Nette\Application\UI\Link($presenter, "User:Default", []);
		$form->addButton("back", USER_EDIT_BACK_BTN_LABEL)
			->setAttribute("class", "btn btn-secondary")
			->setAttribute("onclick", "location.assign('". $link ."')")
			->setAttribute("tabindex", $i+1);;

		return $form;
	}

}
