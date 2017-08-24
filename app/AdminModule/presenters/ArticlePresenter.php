<?php

namespace App\AdminModule\Presenters;

use App\Forms\ArticleForm;
use App\Model\ArticleRepository;
use App\Model\LangRepository;
use App\Model\PicRepository;
use Nette\Forms\Form;

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

	public function actionEdit($id) {
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
		$form->onSubmit[] = $this->articleFormSubmit;

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

	public function articleFormSubmit(Form $form) {

	}

}