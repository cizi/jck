<?php

namespace App\FrontendModule\Presenters;

use App\Controller\EmailController;
use App\Controller\FileController;
use App\Enum\Enum;
use App\Enum\WebWidthEnum;
use App\Forms\ArticleForm;
use App\Model\Entity\ArticleCategoryEntity;
use App\Model\Entity\ArticleContentEntity;
use App\Model\Entity\ArticleEntity;
use App\Model\Entity\ArticleTimetableEntity;
use App\Model\Entity\MenuEntity;
use App\Model\EnumerationRepository;
use App\Model\MenuRepository;
use App\Model\WebconfigRepository;
use Nette\Forms\Form;
use Nette\Http\FileUpload;
use Nette\Utils\ArrayHash;

class SubmitPresenter extends BasePresenter {

	/** @var ArticleForm */
	private $articleForm;

	public function __construct(ArticleForm $articleForm) {
		$this->articleForm = $articleForm;
	}

	/**
	 * @param string $lang
	 * @param string $id
	 */
	public function renderDefault($lang, $id, array $values = null) {
		if ($values != null) {
			$this['submitForm']->setDefaults($values);
		}
		if (empty($lang)) {
			$lang = $this->langRepository->getCurrentLang($this->session);
			$this->redirect("default", [ 'lang' => $lang, 'id' => $id]);
		}

		$userBlocks = [];
		$availableLangs = $this->langRepository->findLanguages();
		// what if link will have the same shortcut like language
		if (isset($availableLangs[$lang]) && ($lang != $this->langRepository->getCurrentLang($this->session))) {
			$this->langRepository->switchToLanguage($this->session, $lang);
			$this->redirect("default", [ 'lang' => $lang, 'id' => $id ]);
		} else {
			if ((empty($id) || ($id == "")) && !empty($lang) && (!isset($availableLangs[$lang]))) {
				$id = $lang;
			}

			// because of sitemap.xml
			$allWebLinks = $this->menuRepository->findAllItems();
			$this->template->webAvailebleLangs = $availableLangs;
			$this->template->availableLinks = $allWebLinks;
			/** @var MenuEntity $menuLink */
			foreach($allWebLinks as $menuLink) {
				if ($menuLink->getLink() == $id) {
					$this->template->currentLink = $menuLink;
				}
			}			}

		$this->template->userBlocks = $userBlocks;
		$this->template->widthEnum = new WebWidthEnum();
		$this->template->articleTimeTableWrongTime = ARTICLE_TIMETABLE_TIME_WRONG_FORMAT;
		$this->template->articleFileUploadMissing = ARTICLE_MAIN_URL_REQ;
		$this->template->menuOrderAction = MenuRepository::MENU_ITEM_ACTION;
	}

	public function createComponentSubmitForm() {
		$form = $this->articleForm->create($this->langRepository->getCurrentLang($this->session), 1);
		unset($form['type']);
		unset($form['validity']);
		unset($form['active']);
		unset($form['pics']);
		unset($form['pic_id']);
		unset($form['place']);
		unset($form['location']);
		unset($form['docsUpload']);
		unset($form['gallery_id']);
		$form['confirm']->caption = SUBMIT_OWN_BUTTON;

		$form->onSuccess[] = $this->submitFormSubmit;

		$renderer = $form->getRenderer();
		$renderer->wrappers['controls']['container'] = NULL;
		$renderer->wrappers['pair']['container'] = 'div class=form-group';
		$renderer->wrappers['pair']['.error'] = 'has-error';
		$renderer->wrappers['control']['container'] = 'div class=col-md-6';
		$renderer->wrappers['label']['container'] = 'div class="col-md-4 control-label"';
		$renderer->wrappers['control']['description'] = 'span class=help-block';
		$renderer->wrappers['control']['errorcontainer'] = 'span class=help-block';
		$form->getElementPrototype()->class('form-horizontal');

		return $form;
	}

	/**
	 * @param Form $form
	 * @param $values
	 */
	public function submitFormSubmit(Form $form, $values) {
		$articleEntity = new ArticleEntity();
		$articleEntity->hydrate((array)$values);

		$error = false;
		$supportedFileFormats = ["jpg", "png", "doc"];
		$calendars = [];
		$mutation = [];
		$categories = [];
		$pics = [];
		foreach($values as $key => $value) {
			if (($value instanceof ArrayHash) && ($key == 'calendar')) {    // timetable
				foreach ($value as $calendarKey => $calendarData) {
					foreach ($calendarData as $calendarItem) {
						$articleTimetableEntity = new ArticleTimetableEntity();
						$articleTimetableEntity->hydrate((array)$calendarItem);
						$calendars[] = $articleTimetableEntity;
					}
				}
			}
			if (($value instanceof ArrayHash) && ($key != 'calendar')) {	// language mutation
				$articleContentEntity = new ArticleContentEntity();
				$articleContentEntity->hydrate((array)$value);
				$articleContentEntity->setLang($key);

				$mutation[] = $articleContentEntity;
			}
			if ((is_array($value)) && ($key == 'menuOrders')) {    // language mutation
				foreach	($value as $menuOrder) {
					$articleCategoryEntity = new ArticleCategoryEntity();
					$articleCategoryEntity->setMenuOrder($menuOrder);
					$categories[] = $articleCategoryEntity;
				}
			}
			if ($key == 'picUrlUpload') {	// jen jeden hlavní obrázek
				/** @var FileUpload $file */
				$file = $value;
				if ($file->name != "") {
					$fileController = new FileController();
					if ($fileController->upload($file, $supportedFileFormats, $this->getHttpRequest()->getUrl()->getBaseUrl()) == false) {
						$error = true;
						break;
					}
					$articleEntity->setPicUrl($fileController->getPathDb());
				}
			}
		}

		$articleEntity->setContents($mutation);
		$articleEntity->setType(EnumerationRepository::TYP_PRISPEVKU_AKCE_ORDER);
		$articleEntity->setValidity(EnumerationRepository::TYP_VALIDITY_FREE);
		if ($error) {
			$flashMessage = sprintf(UNSUPPORTED_UPLOAD_FORMAT, implode(",", $supportedFileFormats));
			$this->flashMessage($flashMessage, "alert-danger");
			$this->redirect("default", $this->langRepository->getCurrentLang($this->session), null, $values);
		} else {
			$insertedBy = ($this->getUser()->isLoggedIn() ? $this->getUser()->getId() : null);
			$articleEntity->setLocation(EnumerationRepository::LOKACE_JIHOCESKY_ORDER);	// máme jen jihočeský
			if ($this->articleRepository->saveCompleteArticle($articleEntity, $insertedBy, $calendars, $categories, $pics) == false) {
				$this->flashMessage(SUBMIT_OWN_FAILED, "alert-danger");
				$this->redirect("default", $this->langRepository->getCurrentLang($this->session), null, $values);
			} else {
				$emailTo = $this->webconfigRepository->getByKey(WebconfigRepository::KEY_CONTACT_FORM_RECIPIENT, WebconfigRepository::KEY_LANG_FOR_COMMON);
				$emailFrom = "admin@jihoceskykalendar.cz";
				$body = sprintf(SUBMIT_OWN_MAIL_BODY, "http://{$_SERVER['HTTP_HOST']}/admin/article/edit/" . $articleEntity->getId());
				EmailController::SendPlainEmail($emailFrom, $emailTo, SUBMIT_OWN_MAIL_SUBJECT, $body);
			}
		}
		$this->flashMessage(SUBMIT_OWN_SAVED, "alert-success");
		$this->redirect("default", $this->langRepository->getCurrentLang($this->session));
	}

}