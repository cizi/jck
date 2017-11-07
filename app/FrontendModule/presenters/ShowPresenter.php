<?php

namespace App\FrontendModule\Presenters;

use App\Forms\FulltextSearchForm;
use App\Model\ArticleRepository;
use App\Model\Entity\ArticleCategoryEntity;
use App\Model\Entity\ArticleEntity;
use App\Model\Entity\MenuEntity;
use App\Model\EnumerationRepository;
use Nette\Forms\Form;
use Nette\Utils\Paginator;

class ShowPresenter extends BasePresenter {

	const ARTICLE_DETAILS_PAGER = 10;

	/** @var FulltextSearchForm */
	private $fulltextSearchForm;

	public function __construct(FulltextSearchForm $fulltextSearchForm) {
		$this->fulltextSearchForm = $fulltextSearchForm;
	}

	/**
	 * @param string $lang
	 * @param int $id
	 * @param string $seoText
	 */
	public function actionDetail($lang, $id, $seoText) {
		$this->checkLanguage($lang);
		$articleEntity = $this->articleRepository->getArticle($id);
		if ($articleEntity) {
			$this->template->article = $articleEntity;
			$this->template->docsUploaded = $this->picRepository->loadDocs($articleEntity->getId());

			$this->template->sliderGalleryPics = $this->findFlexSliderPics($articleEntity);
			$articlesCategories = $articleEntity->getCategories();
			$this->template->wallpaperBanner = $this->bannerRepository->getBannerByType(EnumerationRepository::TYP_BANNERU_WALLPAPER, false, $articlesCategories);
			$this->template->fullBanner = $this->bannerRepository->getBannerByType(EnumerationRepository::TYP_BANNERU_FULL_BANNER, false, $articlesCategories);
			$this->template->halfBanner = $this->bannerRepository->getBannerByType(EnumerationRepository::TYP_BANNERU_HALFBANNER, false, $articlesCategories);
		} else {
			$this->flashMessage(ARTICLE_NOT_FOUND, "alert-danger");
			$this->redirect("Homepage:default");
		}
	}

	/**
	 * @param string $lang
	 * @param int $id
	 * @param string $seoText
	 */
	public function actionDetails($lang, $page = 1) {
		$this->checkLanguage($lang);
		$articles = $this->articleRepository->findActiveArticlesInLang($lang, EnumerationRepository::TYP_PRISPEVKU_CLANEK_ORDER);

		$paginator = new Paginator();
		$paginator->setItemCount(count($articles)); // celkový počet článků
		$paginator->setItemsPerPage(self::ARTICLE_DETAILS_PAGER); // počet položek na stránce
		$paginator->setPage($page); // číslo aktuální stránky

		$articleEntities = array_slice($articles, $paginator->getOffset(), $paginator->getLength());
		$this->template->paginator = $paginator;
		$this->template->articles = $articleEntities;

		$articlesCategories = [];
		$this->template->wallpaperBanner = $this->bannerRepository->getBannerByType(EnumerationRepository::TYP_BANNERU_WALLPAPER, false, $articlesCategories);
		$this->template->fullBanner = $this->bannerRepository->getBannerByType(EnumerationRepository::TYP_BANNERU_FULL_BANNER, false, $articlesCategories);
		$this->template->halfBanner = $this->bannerRepository->getBannerByType(EnumerationRepository::TYP_BANNERU_HALFBANNER, false, $articlesCategories);
	}

	/**
	 * @param string $lang
	 * @param int $id
	 * @param string $seoText
	 */
	public function actionGallery($lang, $id, $seoText) {
		$this->checkLanguage($lang);
		$galleryEntity = $this->galleryRepository->getGallery($id);
		if ($galleryEntity != null) {
			$this->template->gallery = $galleryEntity;
			$this->template->article = $galleryEntity;	// dávám do proměnné article kvůli generování link rel jazykového nastavení

			$articlesCategories = [];
			$this->template->wallpaperBanner = $this->bannerRepository->getBannerByType(EnumerationRepository::TYP_BANNERU_WALLPAPER, false, $articlesCategories);
			$this->template->fullBanner = $this->bannerRepository->getBannerByType(EnumerationRepository::TYP_BANNERU_FULL_BANNER, false, $articlesCategories);
		} else {
			$this->flashMessage(ARTICLE_NOT_FOUND, "alert-danger");
			$this->redirect("Homepage:default");
		}
	}

	public function actionGalleries($lang) {
		$this->checkLanguage($lang);
		$this->template->galleries = $this->galleryRepository->findActiveGalleriesInLang($lang);

		$articlesCategories = [];
		$this->template->wallpaperBanner = $this->bannerRepository->getBannerByType(EnumerationRepository::TYP_BANNERU_WALLPAPER, false, $articlesCategories);
		$this->template->fullBanner = $this->bannerRepository->getBannerByType(EnumerationRepository::TYP_BANNERU_FULL_BANNER, false, $articlesCategories);
	}

	/**
	 * @param string $lang
	 * @param int $id
	 */
	public function actionBanner($lang, $id) {
		$this->checkLanguage($lang);
		$banner = $this->bannerRepository->getBanner($id);
		if ($banner != null) {
			$this->bannerRepository->bannerClicked($banner->getId());
			if (($banner->getUrl() != null) && ($banner->getUrl() != "")) {
				$httpResponse = $this->getHttpResponse();
				$httpResponse->redirect($banner->getUrl());
				$this->terminate();
			}
		}
		$this->redirect("Homepage:default", $lang);
	}

	/**
	 * @param string $lang
	 * @param int $id
	 */
	public function actionEvent($lang, $id, $seoText) {
		$this->checkLanguage($lang);
		$article = $this->articleRepository->getArticle($id);
		if ($article != null) {
			$this->articleRepository->articleClicked($article->getId());
			$places = $this->articleRepository->findActiveArticleByPlaceInLang($lang, $article->getPlace(), EnumerationRepository::TYP_PRISPEVKU_MISTO_ORDER);
			$this->template->eventPlace = (count($places) ? reset($places) : new ArticleEntity());
			$this->template->article = $article;
			$this->template->places = $places;
			$this->template->cities = $this->articleRepository->findActiveArticleBySublocationInLang($lang, $article->getSublocation(), EnumerationRepository::TYP_PRISPEVKU_MISTO_ORDER);
			$this->template->docsUploaded = $this->picRepository->loadDocs($article->getId());
			$this->template->sliderGalleryPics = $this->findFlexSliderPics($article);

			$articlesCategories = $article->getCategories();
			$this->template->wallpaperBanner = $this->bannerRepository->getBannerByType(EnumerationRepository::TYP_BANNERU_WALLPAPER, false, $articlesCategories);
			$this->template->fullBanner = $this->bannerRepository->getBannerByType(EnumerationRepository::TYP_BANNERU_FULL_BANNER, false, $articlesCategories);
			$this->template->halfBanner = $this->bannerRepository->getBannerByType(EnumerationRepository::TYP_BANNERU_HALFBANNER, false, $articlesCategories);
		} else {
			$this->flashMessage(ARTICLE_NOT_FOUND, "alert-danger");
			$this->redirect("Homepage:default");
		}
	}

	/**
	 * @param string $lang
	 * @param int $id
	 * @param string $seoText
	 */
	public function actionPlace($lang, $id, $seoText) {
		$this->checkLanguage($lang);
		$place = $this->articleRepository->getArticle($id);
		if ($place != null) {
			$this->articleRepository->articleClicked($place->getId());
			$this->template->place = (empty($place) ? new ArticleEntity() : $place);
			$this->template->article = $this->template->place;	// dávám do proměnné article kvůli generování link rel jazykového nastavení
			$this->template->places = $this->articleRepository->findActiveArticleByPlaceInLang($lang, $place->getPlace(), EnumerationRepository::TYP_PRISPEVKU_MISTO_ORDER);
			$this->template->textArticles = $this->articleRepository->findActiveArticleByPlaceInLang($lang, $place->getPlace(), EnumerationRepository::TYP_PRISPEVKU_CLANEK_ORDER);;
			$this->template->articles = $this->articleRepository->findActiveArticleByPlaceInLang($lang, $place->getPlace(), EnumerationRepository::TYP_PRISPEVKU_AKCE_ORDER);
			$this->template->docsUploaded = $this->picRepository->loadDocs($place->getId());
			$this->template->sliderGalleryPics = $this->findFlexSliderPics($place);

			$articlesCategories = $place->getCategories();
			$this->template->wallpaperBanner = $this->bannerRepository->getBannerByType(EnumerationRepository::TYP_BANNERU_WALLPAPER, false, $articlesCategories);
			$this->template->fullBanner = $this->bannerRepository->getBannerByType(EnumerationRepository::TYP_BANNERU_FULL_BANNER, false, $articlesCategories);
			$this->template->halfBanner = $this->bannerRepository->getBannerByType(EnumerationRepository::TYP_BANNERU_HALFBANNER, false, $articlesCategories);
		} else {
			$this->flashMessage(ARTICLE_NOT_FOUND, "alert-danger");
			$this->redirect("Homepage:default");
		}
	}

	/**
	 * @param string $lang
	 * @param int $id
	 * @param string $seoText
	 */
	public function actionCity($lang, $id, $seoText) {
		$this->checkLanguage($lang);
		$place = $this->articleRepository->getArticle($id);
		if ($place != null) {
			$this->articleRepository->articleClicked($place->getId());
			$this->template->place = (empty($place) ? new ArticleEntity() : $place);
			$this->template->article = $this->template->place;    // dávám do proměnné article kvůli generování link rel jazykového nastavení
			$this->template->textArticles = $this->articleRepository->findActiveArticleBySublocationInLang($lang, $place->getSublocation(), EnumerationRepository::TYP_PRISPEVKU_CLANEK_ORDER);;
			$this->template->articles = $this->articleRepository->findActiveArticleBySublocationInLang($lang, $place->getSublocation(), EnumerationRepository::TYP_PRISPEVKU_AKCE_ORDER, false);
			$this->template->docsUploaded = $this->picRepository->loadDocs($place->getId());
			$this->template->sliderGalleryPics = $this->findFlexSliderPics($place);

			$articlesCategories = $place->getCategories();
			$this->template->wallpaperBanner = $this->bannerRepository->getBannerByType(EnumerationRepository::TYP_BANNERU_WALLPAPER, false, $articlesCategories);
			$this->template->fullBanner = $this->bannerRepository->getBannerByType(EnumerationRepository::TYP_BANNERU_FULL_BANNER, false, $articlesCategories);
			$this->template->halfBanner = $this->bannerRepository->getBannerByType(EnumerationRepository::TYP_BANNERU_HALFBANNER, false, $articlesCategories);
		} else {
			$this->flashMessage(ARTICLE_NOT_FOUND, "alert-danger");
			$this->redirect("Homepage:default");
		}
	}

	/**
	 * @param string $lang
	 * @param int $id = order v menu
	 * @param string $seoText
	 */
	public function actionCategory($lang, $id, $seoText, $page = 1, $query = null, $sublocation = null) {
		$this->checkLanguage($lang);
		if ($id == null) {	// pokud je kategorie = NULL, hledám všude
			$categories = $this->menuRepository->findAllCategoryOrders();
		} else {	// jinak jen v konkrétní kategorii
			$categories = $this->menuRepository->findDescendantOrders($id, $lang);
			$menuOrder = $this->menuRepository->getMenuEntityByOrder($id, $lang);
		}
		$articles = $this->articleRepository->findActiveArticlesInLangCategory(
			$lang,
			$categories,
			$query,
			$sublocation,
			null
		);

		$paginator = new Paginator();
		$paginator->setItemCount(count($articles)); // celkový počet článků
		$paginator->setItemsPerPage(self::ARTICLE_DETAILS_PAGER); // počet položek na stránce
		$paginator->setPage($page); // číslo aktuální stránky

		$this->template->order = $id;
		$this->template->paginator = $paginator;
		$this->template->query = $query;
		$this->template->seoText = $seoText;
		$this->template->clickedCategory = ($id != null ? $this->menuRepository->getMenuEntityByOrder($id, $lang) : new MenuEntity());

		$articles = array_slice($articles, $paginator->getOffset(), $paginator->getLength());
		$this->template->articles = $articles;
		$this->template->breadcrumbs = ($id != null ? array_reverse($this->menuController->createBreadcrumbs($id, $lang, $this->presenter)) : []);
		if ($query != null) {
			$this['fulltextSearchForm']['search']->setDefaultValue($query);
		}
		if ($sublocation != null) {
			$this['fulltextSearchForm']['sublocation']->setDefaultValue($sublocation);
		}

		$articlesCategories = $this->createArticleCategoryArrayByMenuOrder($categories);
		$this->template->wallpaperBanner = $this->tryGetBanner(EnumerationRepository::TYP_BANNERU_WALLPAPER, $articlesCategories, (isset($menuOrder) ? $menuOrder : new MenuEntity()));
		$this->template->fullBanner = $this->tryGetBanner(EnumerationRepository::TYP_BANNERU_FULL_BANNER, $articlesCategories, (isset($menuOrder) ? $menuOrder : new MenuEntity()));
		$this->template->largeRectangle = $this->tryGetBanner(EnumerationRepository::TYP_BANNERU_LARGE_RECTANGLE, $articlesCategories, (isset($menuOrder) ? $menuOrder : new MenuEntity()));
		$this->template->middleRectangle = $this->tryGetBanner(EnumerationRepository::TYP_BANNERU_MIDDLE_RECTANGLE, $articlesCategories, (isset($menuOrder) ? $menuOrder : new MenuEntity()));
		$this->template->sliderPics = $this->tryFindSliderPics($articlesCategories, (isset($menuOrder) ? $menuOrder : new MenuEntity()));
		$this->template->halfBanner = $this->tryGetBanner(EnumerationRepository::TYP_BANNERU_HALFBANNER, $articlesCategories, (isset($menuOrder) ? $menuOrder : new MenuEntity()));
	}

	public function createComponentFulltextSearchForm() {
		$form = $this->fulltextSearchForm->create($this->langRepository->getCurrentLang($this->session));
		$form->onSuccess[] = $this->submitFulltextSearchForm;

		$renderer = $form->getRenderer();
		$renderer->wrappers['controls']['container'] = NULL;
		$renderer->wrappers['pair']['container'] = 'div class=form-group';
		$renderer->wrappers['pair']['.error'] = 'has-error';
		$renderer->wrappers['control']['container'] = 'div class=col-md-2';
		$renderer->wrappers['label']['container'] = 'div class="col-md-2 control-label"';
		$renderer->wrappers['control']['description'] = 'span class=help-block';
		$renderer->wrappers['control']['errorcontainer'] = 'span class=help-block';
		$form->getElementPrototype()->class('form-horizontal');

		return $form;
	}

	public function submitFulltextSearchForm(Form $form, $values) {
		$path = $this->getHttpRequest()->getUrl()->getPath();
		$pathArray = explode("/", $path);
		$seoText = $pathArray[count($pathArray)-1];
		$order = $pathArray[count($pathArray)-2];
		$page = $this->getHttpRequest()->getQuery('page');
		$search = ($values['search'] == "" ? null : $values['search']);
		$sublocation = ($values['sublocation'] == 0 ? null : $values['sublocation']);
		$this->redirect("category", $this->langRepository->getCurrentLang($this->session), $order, $seoText, $page, $search, $sublocation);
	}

	/**
	 * @param string $lang
	 * @param string $from format d.m.Y
	 * @param string [$to] format d.m.Y
	 */
	public function actionSearchDate($lang, $from, $to, $searchText, $sublocation) {
		$this->checkLanguage($lang);
		try {
			$dateFrom = \DateTime::createFromFormat(ArticleRepository::URL_DATE_MASK, $from);
			if ($dateFrom == false) {
				throw new \Exception();
			}
		} catch (\Exception $e) {
			$dateFrom = new \DateTime();
		}
		try {
			$dateTo = \DateTime::createFromFormat(ArticleRepository::URL_DATE_MASK, $to);
			if ($dateTo == false) {
				throw new \Exception();
			}
		} catch (\Exception $e) {
			$dateTo = null;
		}

		if ($dateFrom != null) {
			$this['mainPageSearchForm']['from']->setDefaultValue($dateFrom->format(ArticleRepository::URL_DATE_MASK));
		}
		if ($dateTo != null) {
			$this['mainPageSearchForm']['to']->setDefaultValue($dateTo->format(ArticleRepository::URL_DATE_MASK));
		}
		if ($sublocation != null) {
			$this['mainPageSearchForm']['sublocation']->setDefaultValue($sublocation);
		}
		$this['mainPageSearchForm']['search']->setDefaultValue($searchText);
		$this->template->articles = $this->articleRepository->findActiveArticlesInLangByDate($lang, $dateFrom, $searchText, $dateTo, EnumerationRepository::TYP_PRISPEVKU_AKCE_ORDER, $sublocation);
		$this->template->requestedAction = "search-date";	// ochcávka, abych nemusel řešit camal case => dash case

		$articlesCategories = [];
		$this->template->wallpaperBanner = $this->bannerRepository->getBannerByType(EnumerationRepository::TYP_BANNERU_WALLPAPER, false, $articlesCategories);
		$this->template->fullBanner = $this->bannerRepository->getBannerByType(EnumerationRepository::TYP_BANNERU_FULL_BANNER, false, $articlesCategories);
		$this->template->halfBanner = $this->bannerRepository->getBannerByType(EnumerationRepository::TYP_BANNERU_HALFBANNER, false, $articlesCategories);
	}

	/**
	 * @return Form
	 */
	public function createComponentMainPageSearchForm() {
		$form = $this->mainPageSearchForm->create($this, $this->langRepository->getCurrentLang($this->session));
		$form->onSuccess[] = $this->mainPageSearchFormSubmit;

		return $form;
	}

	/**
	 * @param Form $form
	 * @param $values
	 */
	public function mainPageSearchFormSubmit(Form $form, $values) {
		$from = (isset($values['from']) ? $values['from'] : null);
		$to = (isset($values['to']) ? $values['to'] : null);
		$search = (isset($values['search']) ? $values['search'] : null);
		$sublocation = ((isset($values['sublocation']) && ($values['sublocation'] != 0)) ? $values['sublocation'] : null);
		if ($from == null) {
			$this->flashMessage(MAIN_SEARCH_REQ_FIELDS, "alert-danger");
			$this->redirect("Homepage:Default");
		} else {
			$this->redirect("SearchDate", $this->langRepository->getCurrentLang($this->session), $from, $to, $search, $sublocation);
		}
	}

	/**
	 * @param int $bannerType
	 * @param ArticleCategoryEntity[] $articlesCategories
	 * @param string $lang
	 * @param MenuEntity $menuOrder
	 * @return \App\Model\Entity\BannerEntity
	 */
	private function tryGetBanner($bannerType, $articlesCategories, $menuOrder) {
		$banner = $this->bannerRepository->getBannerByType($bannerType, false, $articlesCategories);
		while (empty($banner) && isset($menuOrder) && ($menuOrder->getSubmenu() != 0)) {
			$articleCategoryEntity = new ArticleCategoryEntity();
			$articleCategoryEntity->setMenuOrder($menuOrder->getSubmenu());
			$articlesCategories[] = $articleCategoryEntity;
			$menuOrder = $this->menuRepository->getMenuEntityById($menuOrder->getSubmenu());
			$banner = $this->bannerRepository->getBannerByType($bannerType, false, $articlesCategories);
		}

		return $banner;

	}

	/**
	 * @param int $bannerType
	 * @param string $lang
	 * @param MenuEntity $menuOrder
	 * @return \App\Model\Entity\BannerEntity[]
	 */
	private function tryFindSliderPics($articlesCategories, $menuOrder) {
		$sliderPics = $this->articleRepository->findSliderPics(EnumerationRepository::TYP_VALIDITY_TOP, $articlesCategories);
		while (empty($sliderPics) && isset($menuOrder) && ($menuOrder->getSubmenu() != 0)) {
			$articleCategoryEntity = new ArticleCategoryEntity();
			$articleCategoryEntity->setMenuOrder($menuOrder->getSubmenu());
			$articlesCategories[] = $articleCategoryEntity;
			$menuOrder = $this->menuRepository->getMenuEntityById($menuOrder->getSubmenu());
			$sliderPics = $this->articleRepository->findSliderPics(EnumerationRepository::TYP_VALIDITY_TOP, $articlesCategories);
		}

		return $sliderPics;
	}

	/**
	 * Najde obrázky pro flex slider
	 * @param ArticleEntity $articleEntity
	 * @return array
	 */
	private function findFlexSliderPics(ArticleEntity $articleEntity) {
		if (empty($articleEntity->getPicUrl())) {
			if (!empty($articleEntity->getPicId())) {
				$pics[] = $this->picRepository->getById($articleEntity->getPicId())->getPath();
			} else {
				$pics = [];
			}
		} else {
			$pics[] = $articleEntity->getPicUrl();
		}

		if ($articleEntity->getGalleryId() != null) {
			$galleryEntity = $this->galleryRepository->getGallery($articleEntity->getGalleryId());
			foreach ($galleryEntity->getPics() as $pic) {
				$pics[] = $this->picRepository->getById($pic->getSharedPicId())->getPath();
			}
		}

		return $pics;
	}
}