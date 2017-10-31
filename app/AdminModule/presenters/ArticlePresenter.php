<?php

namespace App\AdminModule\Presenters;

use App\Controller\FileController;
use App\Enum\SharedFileEnum;
use App\Forms\ArticleFilterForm;
use App\Forms\ArticleForm;
use App\Model\ArticleTimetableRepository;
use App\Model\Entity\ArticleCategoryEntity;
use App\Model\Entity\ArticleContentEntity;
use App\Model\Entity\ArticleEntity;
use App\Model\Entity\ArticleTimetableEntity;
use App\Model\Entity\PicEntity;
use App\Model\EnumerationRepository;
use App\Model\MenuRepository;
use App\Model\UserRepository;
use Nette\Forms\Form;
use Nette\Http\FileUpload;
use Nette\Utils\ArrayHash;

class ArticlePresenter extends SignPresenter {

	/** @persistent */
	public $filter;

	/** @var ArticleForm */
	private $articleForm;

	/** @var ArticleFilterForm */
	private $articleFilterForm;

	/** @var UserRepository */
	private $userRepository;

	/** @var ArticleTimetableRepository */
	private $articleTimetableRepository;

	public function __construct(
		ArticleForm $articleForm,
		ArticleFilterForm $articleFilterForm,
		UserRepository $userRepository,
		ArticleTimetableRepository $articleTimetableRepository
	) {
		$this->articleForm = $articleForm;
		$this->articleFilterForm = $articleFilterForm;
		$this->userRepository = $userRepository;
		$this->articleTimetableRepository = $articleTimetableRepository;
	}

	public function actionDefault($id) {
		$filter = $this->decodeFilterFromQuery();
		$this['articleFilterForm']->setDefaults($filter);

		$currentLang = $this->langRepository->getCurrentLang($this->session);
		$this->template->currentLang = $currentLang;
		$this->template->userRepo = $this->userRepository;
		$this->template->enumRepo = $this->enumerationRepository;
		$this->template->menuRepo = $this->menuRepository;
		$this->template->articles = $this->articleRepository->findArticlesInLang($currentLang, null, $filter);
		$this->template->typPrispevkuAkceOrder = EnumerationRepository::TYP_PRISPEVKU_AKCE_ORDER;
	}

	public function actionEdit($id, array $values = null) {
		if ($values != null) {
			$this['articleForm']->setDefaults($values);
		}
		if (!empty($id)) {
			$defaults = $this->articleRepository->getArticleForEdit($id);

			$this['articleForm']['id']->setValue($id);
			unset($defaults['inserted_by']);
			$this['articleForm']->setDefaults($defaults);

			$calendars = $this->articleTimetableRepository->findCalendars($id);
			/** @var ArticleTimetableEntity $calendar */
			foreach ($calendars as $calendar) {
				$this['articleForm']['calendar']['calendar'][$calendar->getId()]->setDefaults($calendar->extractForm());
			}
			$this->template->article = $this->articleRepository->getArticle($id);
		}

		$this->template->articleId = $id;
		$this->template->blockPics = $this->picRepository->load();
		$this->template->docsUploaded = $this->picRepository->loadDocs($id);

		$this->template->articleTypeAction = EnumerationRepository::TYP_PRISPEVKU_AKCE_ORDER;
		$this->template->articleTypeArticle = EnumerationRepository::TYP_PRISPEVKU_CLANEK_ORDER;
		$this->template->articleTypePlace = EnumerationRepository::TYP_PRISPEVKU_MISTO_ORDER;
		$this->template->menuOrderAction = MenuRepository::MENU_ITEM_ACTION;
		$this->template->menuOrderArticle = MenuRepository::MENU_ITEM_ARTICLE;
		$this->template->menuOrderPlace = MenuRepository::MENU_ITEM_PLACE;

		$this->template->currentLang = $this->langRepository->getCurrentLang($this->session);
		$this->template->articleTimeTableWrongTime = ARTICLE_TIMETABLE_TIME_WRONG_FORMAT;
		$this->template->articleFileUploadMissing = ARTICLE_MAIN_URL_REQ;
		$this->template->availableAddresses = $this->articleRepository->findAddresses();
	}

	/**
	 * @param int $id
	 */
	public function actionDelete($id) {
		$this->articleRepository->deleteArticle($id);
		$this->redirect("default");
	}

	/**
	 * @param int $id
	 */
	public function actionCopyArticle($id) {
		$article = $this->articleRepository->getArticle($id);
		if ($article != null) {
			$newArticle = clone($article);
			$newArticle->setId(null);
			$newArticle->setClickCounter(0);
			$newArticle->setShowCounter(0);
			foreach ($newArticle->getContents() as $content) {
				$content->setId(null);
			}
			if ($this->articleRepository->saveCompleteArticle($newArticle, $this->getUser()->getId(), $newArticle->getTimetables(), $newArticle->getCategories()) == true) {
				$files = $this->picRepository->loadDocs($article->getId());
				/** @var PicEntity $file */
				foreach ($files as $file) {
					$newFile = clone($file);
					$newFile->setId(null);	// vynuluji ID, aby se přiřadilo nové
					$newFile->setArticleId($newArticle->getId());
					$this->picRepository->save($newFile);
				}

				$this->flashMessage(ARTICLE_DUPLICATE_OK, "alert-success");
				$this->redirect("edit", $newArticle->getId());
			} else {
				$this->flashMessage(ARTICLE_DUPLICATE_FAILED, "alert-danger");
				$this->redirect("default");
			}
		} else {
			$this->redirect("default");
		}

	}

	public function createComponentArticleForm() {
		$form = $this->articleForm->create($this->langRepository->getCurrentLang($this->session));
		unset($form['contact_email']);
		$form->onSuccess[] = $this->articleFormSubmit;

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
	 * @param ArrayHash $values
	 */
	public function articleFormSubmit($form, $values) {
		$articleEntity = new ArticleEntity();
		$articleEntity->hydrate((array)$values);

		$error = false;
		$supportedFileFormats = ["jpg", "png", "gif", "jpeg"];
		$supportedDocFileFormats = ["pdf", "doc", "docx", "jpg", "png"];
		$calendars = [];
		$mutation = [];
		$categories = [];
		$docs = [];
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
			if (is_array($value) && ($key == 'docsUpload')) {	// ostatní přílohy
				/** @var FileUpload $file */
				foreach ($value as $file) {
					if ($file->name != "") {
						$fileController = new FileController();
						if ($fileController->upload($file, $supportedDocFileFormats, $this->getHttpRequest()->getUrl()->getBaseUrl()) == false) {
							$error = true;
							break;
						}
						$doc = new PicEntity();
						$doc->setPath($fileController->getPathDb());
						$doc->setFileType(SharedFileEnum::DOC);

						$docs[] = $doc;
					}
				}
			}
		}

		$articleEntity->setContents($mutation);
		if ($error) {
			$flashMessage = sprintf(UNSUPPORTED_UPLOAD_FORMAT, implode(",", $supportedFileFormats));
			$this->flashMessage($flashMessage, "alert-danger");
			$this->redirect("edit", null, $values);
		} else {
			if ($this->articleRepository->saveCompleteArticle($articleEntity, $this->getUser()->getId(), $calendars, $categories, $docs) == false) {
				$this->flashMessage(ARTICLE_SAVE_FAILED, "alert-danger");
				$this->redirect("edit", null, $values);
			}
		}
		$this->redirect("default");
	}

	/**
	 * AJAX pro aktivace / deaktivace uživatele
	 */
	public function handleActiveSwitch() {
		$data = $this->request->getParameters();
		$idArticle = $data['idArticle'];
		$switchTo = (!empty($data['to']) && $data['to'] == "false" ? false : true);

		if ($switchTo) {
			$this->articleRepository->setArticleActive($idArticle);
		} else {
			$this->articleRepository->setArticleInactive($idArticle);
		}

		$this->terminate();
	}

	/**
	 * @param int $picId
	 */
	public function actionDeletePic($articleId, $picId) {
		if ($this->picRepository->delete($picId) == false) {
			$this->flashMessage(PIC_NOT_POSSIBLE_DELETE, "alert-danger");
		} else {
			$this->flashMessage(PIC_DELETE_DELETED, "alert-success");
		}
		$this->redirect("edit", $articleId);
	}

	public function createComponentArticleFilterForm() {
		$form = $this->articleFilterForm->create($this->langRepository->getCurrentLang($this->session));
		$form->onSuccess[] = $this->articleFilterFormSubmit;

		$renderer = $form->getRenderer();
		$renderer->wrappers['controls']['container'] = NULL;
		$renderer->wrappers['pair']['container'] = 'div class=form-group';
		$renderer->wrappers['pair']['.error'] = 'has-error';
		$renderer->wrappers['control']['container'] = 'div class="col-xs-6 col-sm-3 col-md-3 col-lg-4"';
		$renderer->wrappers['label']['container'] = 'div class="col-xs-6 col-sm-3 col-md-3 col-lg-2 control-label"';
		$renderer->wrappers['control']['description'] = 'span class=help-block';
		$renderer->wrappers['control']['errorcontainer'] = 'span class=help-block';

		return $form;
	}

	/**
	 * @param Form $form
	 * @param $values
	 */
	public function articleFilterFormSubmit(Form $form) {
		$filter = "1&";
		$data = $form->getHttpData();

		foreach ($data as $key => $value) {
			if (is_array($value)) {	// kategorie
				$filter .= $key . "=";
				for ($i = 0; $i < count($value); $i++) {
					$filter .= $value[$i];
					$filter .= (($i+1) == count($value) ? "&" : ",");
				}
			} else if ($value != "") {
				$filter .= $key . "=" . $value . "&";
			}
		}
		$this->filter = $filter;
		$this->redirect("default");
	}
}