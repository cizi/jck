<?php

namespace App\AdminModule\Presenters;

use App\Controller\FileController;
use App\Forms\ArticleForm;
use App\Model\ArticleRepository;
use App\Model\Entity\ArticleContentEntity;
use App\Model\Entity\ArticleEntity;
use App\Model\Entity\PicEntity;
use App\Model\LangRepository;
use App\Model\PicRepository;
use Nette\Forms\Form;
use Nette\Http\FileUpload;
use Nette\Utils\ArrayHash;

class ArticlePresenter extends SignPresenter {

	/** @var ArticleRepository */
	private $articleRepository;

	/** @var LangRepository */
	private $langRepository;

	/** @var ArticleForm */
	private $articleForm;

	/** @var PicRepository */
	private $picRepository;

	public function __construct(
		ArticleRepository $articleRepository,
		LangRepository $langRepository,
		ArticleForm $articleForm,
		PicRepository $picRepository
	) {
		$this->articleRepository = $articleRepository;
		$this->langRepository = $langRepository;
		$this->articleForm = $articleForm;
		$this->picRepository = $picRepository;
	}

	public function actionDefault($id) {
		$currentLang = $this->langRepository->getCurrentLang($this->session);
		$this->template->articles = $this->articleRepository->findArticlesInLang($currentLang);
	}

	public function actionEdit($id, array $values = null) {
		if ($values != null) {
			$this['articleForm']->setDefaults($values);
		}
		if (!empty($id)) {
			$defaults = $this->blockRepository->getEditArray($id);
			$this['articleForm']['id']->setValue($id);
			$this['articleForm']->setDefaults($defaults);
		}

		$this->template->articleId = $id;
		$this->template->blockPics = $this->picRepository->load();
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
		$mutation = [];
		$pics = [];
		foreach($values as $key => $value) {
			if ($value instanceof ArrayHash) {	// language mutation
				$articleContentEntity = new ArticleContentEntity();
				$articleContentEntity->hydrate((array)$value);
				$articleContentEntity->setLang($key);

				$mutation[] = $articleContentEntity;
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
			if ($this->articleRepository->saveCompleteArticle($articleEntity, $this->getUser()->getId(), $pics) == false) {
				$this->flashMessage(ARTICLE_SAVE_FAILED, "alert-danger");
				$this->redirect("edit", null, $values);
			}
		}
		$this->redirect("default");
	}

	public function actionDeletePic($picId) {
		// TODO
	}

}