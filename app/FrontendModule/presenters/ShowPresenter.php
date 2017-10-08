<?php

namespace App\FrontendModule\Presenters;

use App\Forms\FulltextSearchForm;
use App\Model\ArticleRepository;
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
		}
	}

	/**
	 * @param string $lang
	 * @param int $id
	 * @param string $seoText
	 */
	public function actionDetails($lang, $page = 1) {
		$this->checkLanguage($lang);
		$articles = $this->articleRepository->findActiveArticlesInLang(
			$lang,
			EnumerationRepository::TYP_PRISPEVKU_CLANEK_ORDER
		);

		$paginator = new Paginator();
		$paginator->setItemCount(count($articles)); // celkový počet článků
		$paginator->setItemsPerPage(self::ARTICLE_DETAILS_PAGER); // počet položek na stránce
		$paginator->setPage($page); // číslo aktuální stránky

		$articleEntities = array_slice($articles, $paginator->getOffset(), $paginator->getLength());
		$this->template->paginator = $paginator;
		$this->template->articles = $articleEntities;
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
		}
	}

	public function actionGalleries($lang) {
		$this->checkLanguage($lang);
		$this->template->galleries = $this->galleryRepository->findActiveGalleriesInLang($lang);
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
			$this->template->eventPlace = reset($places);
			$this->template->article = $article;
			$this->template->places = $places;
			$this->template->cities = $this->articleRepository->findActiveArticleBySublocationInLang($lang, $article->getSublocation(), EnumerationRepository::TYP_PRISPEVKU_MISTO_ORDER);
			$this->template->docsUploaded = $this->picRepository->loadDocs($article->getId());
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
		$this->articleRepository->articleClicked($place->getId());
		$this->template->place = (empty($place) ? new ArticleEntity() : $place);
		$this->template->textArticles = $this->articleRepository->findActiveArticleByPlaceInLang($lang, $place->getPlace(), EnumerationRepository::TYP_PRISPEVKU_CLANEK_ORDER);;
		$this->template->articles = $this->articleRepository->findActiveArticleByPlaceInLang($lang, $place->getPlace(), EnumerationRepository::TYP_PRISPEVKU_AKCE_ORDER);
		$this->template->docsUploaded = $this->picRepository->loadDocs($place->getId());

		if (empty($place->getPicUrl())) {
			$pics = [];
		} else {
			$pics[] = $place->getPicUrl();
		}

		if ($place->getGalleryId() != null) {
			$galleryEntity = $this->galleryRepository->getGallery($place->getGalleryId());
			foreach ($galleryEntity->getPics() as $pic) {
				$pics[] = $this->picRepository->getById($pic->getSharedPicId())->getPath();
			}
		}
		$this->template->sliderGalleryPics = $pics;
	}

	/**
	 * @param string $lang
	 * @param int $id
	 * @param string $seoText
	 */
	public function actionCity($lang, $id, $seoText) {
		$this->checkLanguage($lang);
		$place = $this->articleRepository->getArticle($id);
		$this->articleRepository->articleClicked($place->getId());
		$this->template->place = (empty($place) ? new ArticleEntity() : $place);
		$this->template->textArticles = $this->articleRepository->findActiveArticleBySublocationInLang($lang, $place->getSublocation(), EnumerationRepository::TYP_PRISPEVKU_CLANEK_ORDER);;
		$this->template->articles = $this->articleRepository->findActiveArticleBySublocationInLang($lang, $place->getSublocation(), EnumerationRepository::TYP_PRISPEVKU_AKCE_ORDER, false);
		$this->template->docsUploaded = $this->picRepository->loadDocs($place->getId());
		$this->setView('place');
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
}