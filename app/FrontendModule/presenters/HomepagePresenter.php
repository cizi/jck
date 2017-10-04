<?php

namespace App\FrontendModule\Presenters;

use App\Controller\FileController;
use App\Model\Entity\BlockContentEntity;
use App\Model\Entity\MenuEntity;
use App\Model\EnumerationRepository;
use App\Model\PicRepository;
use Nette;
use App\Enum\WebWidthEnum;
use App\Model\WebconfigRepository;
use Nette\Http\FileUpload;

class HomepagePresenter extends BasePresenter {

	const MAX_TEXT_ARTICLES = 4;
	const MAX_GALLERIES = 4;

	/**
	 * @param string $lang
	 * @param string $id
	 */
	public function renderDefault($lang, $id) {
		if (empty($lang)) {
			$lang = $this->langRepository->getCurrentLang($this->session);
			$this->redirect("default", [ 'lang' => $lang, 'id' => $id]);
		}

		// what if link will have the same shortcut like language
		$availableLangs = $this->langRepository->findLanguages();
		if (isset($availableLangs[$lang]) && ($lang != $this->langRepository->getCurrentLang($this->session))) {
			$this->langRepository->switchToLanguage($this->session, $lang);
			$this->redirect("default", [ 'lang' => $lang, 'id' => $id ]);
		} else {
			if ((empty($id) || ($id == "")) && !empty($lang) && (!isset($availableLangs[$lang]))) {
				$id = $lang;
			}
			// because of sitemap.xml
			$allWebLinks = $this->menuRepository->findAllItems();
			$this->template->availableLinks = $allWebLinks;
			/** @var MenuEntity $menuLink */
			foreach($allWebLinks as $menuLink) {
				if ($menuLink->getLink() == $id) {
					$this->template->currentLink = $menuLink;
				}
			}
		}

		$this->template->widthEnum = new WebWidthEnum();
		$this->template->textArticles = $this->articleRepository->findActiveArticlesInLang($lang, EnumerationRepository::TYP_PRISPEVKU_CLANEK_ORDER);
		$this->template->galleries = $this->galleryRepository->findActiveGalleriesInLang($lang);
		$this->template->topRandomEvents = $this->articleRepository->findActiveArticlesInLang($lang, EnumerationRepository::TYP_PRISPEVKU_AKCE_ORDER);
		$this->template->maxTextArticles = self::MAX_TEXT_ARTICLES;
		$this->template->maxGalleries = self::MAX_GALLERIES;
	}

	/**
	 * @return Nette\Application\UI\Form
	 */
	public function createComponentMainPageSearchForm() {
		$form = $this->mainPageSearchForm->create($this, $this->langRepository->getCurrentLang($this->session));
		$form->onSuccess[] = $this->mainPageSearchFormSubmit;

		return $form;
	}

	/**
	 * @param Nette\Forms\Form $form
	 * @param $values
	 */
	public function mainPageSearchFormSubmit(Nette\Forms\Form $form, $values) {
		$from = (isset($values['from']) ? $values['from'] : null);
		$to = (isset($values['to']) ? $values['to'] : null);
		$search = (isset($values['search']) ? $values['search'] : null);
		$sublocation = ((isset($values['sublocation']) && ($values['sublocation'] != 0)) ? $values['sublocation'] : null);

		if ($from == null) {
			$this->flashMessage(MAIN_SEARCH_REQ_FIELDS, "alert-danger");
			$this->redirect("Default");
		} else {
			$this->redirect("Show:SearchDate", $this->langRepository->getCurrentLang($this->session), $from, $to, $search, $sublocation);
		}
	}
}
