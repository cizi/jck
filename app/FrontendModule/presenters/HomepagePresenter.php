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
		$this->checkLanguage($lang);
		$this->articleRepository->deactivateOldEvents($lang);
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
		unset($this->template->requestedAction);	// odnastavím proměnnou z base presenteru (toto je homepage celého webu)
		$this->template->widthEnum = new WebWidthEnum();
		$this->template->textArticles = $this->articleRepository->findActiveArticlesInLang($lang, EnumerationRepository::TYP_PRISPEVKU_CLANEK_ORDER);
		$this->template->galleries = $this->galleryRepository->findActiveGalleriesInLang($lang, true);
		$this->template->topRandomEvents = $this->articleRepository->findActiveArticlesInLang($lang, EnumerationRepository::TYP_PRISPEVKU_AKCE_ORDER);
		$this->template->maxTextArticles = self::MAX_TEXT_ARTICLES;
		$this->template->maxGalleries = self::MAX_GALLERIES;

		$this->template->sliderPics = $this->articleRepository->findSliderPics();
		$this->template->wallpaperBanner = $this->bannerRepository->getBannerByType(EnumerationRepository::TYP_BANNERU_WALLPAPER, true);
		$this->template->fullBanner = $this->bannerRepository->getBannerByType(EnumerationRepository::TYP_BANNERU_FULL_BANNER, true);
		$this->template->largeRectangle = $this->bannerRepository->getBannerByType(EnumerationRepository::TYP_BANNERU_LARGE_RECTANGLE, true);
		$this->template->middleRectangle = $this->bannerRepository->getBannerByType(EnumerationRepository::TYP_BANNERU_MIDDLE_RECTANGLE, true);
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
