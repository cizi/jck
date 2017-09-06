<?php

namespace App\AdminModule\Presenters;

use App\Controller\FileController;
use App\Forms\ArticleForm;
use App\Model\ArticleRepository;
use App\Model\ArticleTimetableRepository;
use App\Model\Entity\ArticleContentEntity;
use App\Model\Entity\ArticleEntity;
use App\Model\Entity\ArticleTimetableEntity;
use App\Model\Entity\PicEntity;
use App\Model\EnumerationRepository;
use App\Model\LangRepository;
use App\Model\PicRepository;
use App\Model\UserRepository;
use Kdyby\Replicator\Container;
use Nette\Forms\Form;
use Nette\Http\FileUpload;
use Nette\Utils\ArrayHash;

class ArticlePresenter extends SignPresenter {

	/** @var ArticleForm */
	private $articleForm;

	/** @var PicRepository */
	private $picRepository;

	/** @var UserRepository */
	private $userRepository;

	/** @var ArticleTimetableRepository */
	private $articleTimetableRepository;

	public function __construct(
		ArticleForm $articleForm,
		PicRepository $picRepository,
		UserRepository $userRepository,
		ArticleTimetableRepository $articleTimetableRepository
	) {
		$this->articleForm = $articleForm;
		$this->picRepository = $picRepository;
		$this->userRepository = $userRepository;
		$this->articleTimetableRepository = $articleTimetableRepository;
	}

	public function actionDefault($id) {
		$currentLang = $this->langRepository->getCurrentLang($this->session);
		$this->template->currentLang = $currentLang;
		$this->template->userRepo = $this->userRepository;
		$this->template->enumRepo = $this->enumerationRepository;
		$this->template->menuRepo = $this->menuRepository;
		$this->template->articles = $this->articleRepository->findArticlesInLang($currentLang);
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
			unset($defaults['inserted_timestamp']);
			$this['articleForm']->setDefaults($defaults);

			$calendars = $this->articleTimetableRepository->findCalendars($id);
			/** @var ArticleTimetableEntity $calendar */
			foreach ($calendars as $calendar) {
				$this['articleForm']['calendar']['calendar'][$calendar->getId()]->setDefaults($calendar->extractForm());
			}
		}

		$this->template->articleId = $id;
		$this->template->blockPics = $this->picRepository->load();
		$this->template->articleTypeAction = EnumerationRepository::TYP_PRISPEVKU_AKCE_ORDER;
		$this->template->currentLang = $this->langRepository->getCurrentLang($this->session);
		$this->template->articleTimeTableWrongTime = ARTICLE_TIMETABLE_TIME_WRONG_FORMAT;
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
			foreach ($newArticle->getContents() as $content) {
				$content->setId(null);
			}
			if ($this->articleRepository->saveCompleteArticle($newArticle, $this->getUser()->getId(), $newArticle->getTimetables()) == true) {
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
		$supportedFileFormats = ["jpg", "png", "doc"];
		$calendars = [];
		$mutation = [];
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
			if (is_array($value)) {	// obrázky
				/** @var FileUpload $file */
				foreach ($value as $file) {
					if ($file->name != "") {
						$fileController = new FileController();
						if ($fileController->upload($file, $supportedFileFormats, $this->getHttpRequest()->getUrl()->getBaseUrl()) == false) {
							$error = true;
							break;
						}

						$blockPic = new PicEntity();
						$blockPic->setPath($fileController->getPathDb());
						$pics[] = $blockPic;
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
			if ($this->articleRepository->saveCompleteArticle($articleEntity, $this->getUser()->getId(), $calendars, $pics) == false) {
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
		}
		$this->redirect("edit", $articleId);
	}

}