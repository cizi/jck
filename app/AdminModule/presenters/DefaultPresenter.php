<?php

namespace App\AdminModule\Presenters;

use App\Controller\EmailController;
use App\Forms\PasswordResetForm;
use App\Model\LangRepository;
use App\Model\UserRepository;
use App\Forms\SignForm;
use App\FrontendModule\Presenters\BasePresenter;
use App\Model\WebconfigRepository;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;
use App\AdminModule\Presenters;
use Tester\CodeCoverage\PhpParser;

class DefaultPresenter extends BasePresenter {

	/** @var SignForm */
	public $singInForm;

	/** @var UserRepository */
	public $userRepository;

	/** @var LangRepository $langRepository */
	private $langRepository;

	/** @var PasswordResetForm */
	private $passwordResetForm;

	/** @var WebconfigRepository */
	private $webconfigRepository;

	/**
	 * DefaultPresenter constructor.
	 * @param SignForm $signForm
	 * @param UserRepository $userRepository
	 * @param LangRepository $langRepository
	 * @param PasswordResetForm $passwordResetForm
	 */
	public function __construct(
		SignForm $signForm,
		UserRepository $userRepository,
		LangRepository $langRepository,
		PasswordResetForm $passwordResetForm,
		WebconfigRepository $webconfigRepository
	) {
		$this->singInForm = $signForm;
		$this->userRepository = $userRepository;
		$this->langRepository = $langRepository;
		$this->passwordResetForm = $passwordResetForm;
		$this->webconfigRepository = $webconfigRepository;
	}

	/**
	 * Already logged in, redirect do dashboard
	 */
	public function actionDefault() {
		if ($this->user->isLoggedIn()) {
			$this->redirect('Dashboard:Default');
		}
	}

	/**
	 * Sign-in form factory.
	 * @return Form
	 */
	public function createComponentSignInForm(){
		$form = $this->singInForm->create();

		$langs = $this->langRepository->findLanguages();
		if (count($langs) == 0) {
			$form['lang']->setAttribute("style", "display: none");
		} else {
			$form['lang']->setItems($langs);
		}

		$form->onSuccess[] = $this->formSucceeded;

		return $form;
	}

	/**
	 * @param Form $form
	 * @param $values
	 */
	public function formSucceeded(Form $form, $values) {
		if ($values->remember) {
			$this->user->setExpiration('14 days', false);
		} else {
			$this->user->setExpiration('20 minutes', true);
		}

		try {
			$credentials = ['email' => $values['login'], 'password' => $values['password']];
			$identity = $this->user->getAuthenticator()->authenticate($credentials);
			$this->user->login($identity);
			$this->userRepository->updateLostLogin($identity->getId());

			$availableLnags = $this->langRepository->findLanguages();
			if (isset($values['lang']) && isset($availableLnags[$values['lang']])) {
				$this->langRepository->switchToLanguage($this->session, $values['lang']);
			}
			$this->redirect("Dashboard:Default");
		} catch (\Nette\Security\AuthenticationException $e) {
			$form->addError(ADMIN_LOGIN_FAILED);
		}
	}

	public function createComponentPasswordResetForm() {
		$form = $this->passwordResetForm->create();
		$form->onSubmit[] = $this->resetUserPassword;

		return $form;
	}

	/**
	 * @param Form $form
	 */
	public function resetUserPassword(Form $form) {
		$values = $form->getHttpData();
		if (isset($values["login"]) && $values["login"] != "") {
			$user = $this->userRepository->getUserByEmail(trim($values["login"]));
			if ($user != null) {
				try {
					$newPass = $this->userRepository->resetUserPassword($user);
					$emailFrom = $this->webconfigRepository->getByKey(WebconfigRepository::KEY_CONTACT_FORM_RECIPIENT, WebconfigRepository::KEY_LANG_FOR_COMMON);
					$subject = sprintf(ADMIN_LOGIN_PASSWORD_CHANGED_EMAIL_SUBJECT, $this->getHttpRequest()->getUrl()->getBaseUrl());
					$body = sprintf(ADMIN_LOGIN_PASSWORD_CHANGED_EMAIL_BODY, $this->getHttpRequest()->getUrl()->getBaseUrl(), $newPass);
					EmailController::SendPlainEmail($emailFrom, $user->getEmail(), $subject, $body);

					$this->flashMessage(ADMIN_LOGIN_RESET_SUCCESS, "alert-success");
					$this->redirect("default");
				} catch (\Exception $e) {
					if ($e instanceof AbortException) {
						throw $e;
					} else {
						$this->flashMessage(ADMIN_LOGIN_RESET_FAILED, "alert-danger");
						$form->addError(ADMIN_LOGIN_RESET_FAILED);
					}
				}
			} else {
				$this->flashMessage(ADMIN_LOGIN_RESET_PASSWORD_EMAIL_FAIL, "alert-danger");
				$form->addError(ADMIN_LOGIN_RESET_PASSWORD_EMAIL_FAIL);
			}
		}
	}

	/**
	 * Odhlásí uživatele
	 */
	public function actionOut(){
		$this->getUser()->logout();
		$this->flashMessage(ADMIN_LOGIN_UNLOGGED);
		$this->redirect('default');
	}
}