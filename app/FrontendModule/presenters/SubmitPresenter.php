<?php

namespace App\FrontendModule\Presenters;

use App\Enum\WebWidthEnum;
use App\Forms\ArticleForm;
use App\Model\Entity\MenuEntity;
use Nette\Forms\Form;

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
	public function renderDefault($lang, $id) {
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
			if (empty($id) || ($id == "")) {    // try to find default
				$userBlocks[] = $this->getDefaultBlock();
			} else {
				$userBlocks = $this->blockRepository->findAddedBlockFronted($id,
					$this->langRepository->getCurrentLang($this->session));
				if (empty($userBlocks)) {
					$userBlocks[] = $this->getDefaultBlock();
				}
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
	}

	public function createComponentSubmitForm() {
		$form = $this->articleForm->create($this->langRepository->getCurrentLang($this->session));
		unset($form['type']);
		unset($form['validity']);
		unset($form['active']);
		unset($form['pics']);
		unset($form['pic_id']);
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

	public function submitFormSubmit(Form $form, $values) {

	}

}