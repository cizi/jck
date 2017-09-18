<?php

namespace App\FrontendModule\Presenters;

use App\Forms\FulltextSearchForm;
use App\Model\Entity\ArticleEntity;
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
		$articleEntity = $this->articleRepository->getArticle($id);
		if ($articleEntity) {
			$this->template->article = $articleEntity;
		}
	}

	/**
	 * @param string $lang
	 * @param int $id
	 * @param string $seoText
	 */
	public function actionDetails($lang, $page = 1) {
		$articlesCount = $this->articleRepository->getActiveArticlesInLangCount($lang, EnumerationRepository::TYP_PRISPEVKU_CLANEK_ORDER);

		$paginator = new Paginator();
		$paginator->setItemCount($articlesCount); // celkový počet článků
		$paginator->setItemsPerPage(self::ARTICLE_DETAILS_PAGER); // počet položek na stránce
		$paginator->setPage($page); // číslo aktuální stránky

		$articleEntities = $this->articleRepository->findActiveArticlesInLang(
			$lang,
			EnumerationRepository::TYP_PRISPEVKU_CLANEK_ORDER,
			$paginator->getLength(),
			$paginator->getOffset()
		);
		$this->template->paginator = $paginator;
		$this->template->articles = $articleEntities;
	}

	/**
	 * @param string $lang
	 * @param int $id
	 * @param string $seoText
	 */
	public function actionGallery($lang, $id, $seoText) {
		$galleryEntity = $this->galleryRepository->getGallery($id);
		if ($galleryEntity != null) {
			$this->template->gallery = $galleryEntity;
		}
	}

	public function actionGalleries($lang) {
		$this->template->galleries = $this->galleryRepository->findActiveGalleriesInLang($lang);
	}

	/**
	 * @param string $lang
	 * @param int $id
	 */
	public function actionBanner($lang, $id) {
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
		$article = $this->articleRepository->getArticle($id);
		if ($article != null) {
			$this->articleRepository->articleClicked($article->getId());
			$this->template->article = $article;
			$this->template->places = $this->articleRepository->findActiveArticleByPlaceInLang($lang, $article->getSublocation(), EnumerationRepository::TYP_PRISPEVKU_MISTO_ORDER);
		}
	}

	/**
	 * @param string $lang
	 * @param int $id
	 * @param string $seoText
	 */
	public function actionPlace($lang, $id, $seoText) {
		$places = $this->articleRepository->findActiveArticleByPlaceInLang($lang, $id, EnumerationRepository::TYP_PRISPEVKU_MISTO_ORDER);
		$this->template->place = (empty($places) ? new ArticleEntity() : reset($places));
		$this->template->events = $this->articleRepository->findActiveArticleByPlaceInLang($lang, $id, EnumerationRepository::TYP_PRISPEVKU_AKCE_ORDER);
		$this->template->articles = $this->articleRepository->findActiveArticleByPlaceInLang($lang, $id, EnumerationRepository::TYP_PRISPEVKU_CLANEK_ORDER);;
	}

	/**
	 * @param string $lang
	 * @param int $id
	 * @param string $seoText
	 */
	public function actionEventOnPlace($lang, $id, $seoText) {
		// TODO
	}

	/**
	 * @param string $lang
	 * @param int $id
	 * @param string $seoText
	 */
	public function actionCategory($lang, $id, $seoText, $page = 1, $query = null) {
		$categories = $this->menuRepository->findPredecessors($id, $lang);
		$articlesCount = $this->articleRepository->getActiveArticlesInLangCategoryCount($lang, $categories, $query);

		$paginator = new Paginator();
		$paginator->setItemCount($articlesCount); // celkový počet článků
		$paginator->setItemsPerPage(self::ARTICLE_DETAILS_PAGER); // počet položek na stránce
		$paginator->setPage($page); // číslo aktuální stránky

		$this->template->order = $id;
		$this->template->paginator = $paginator;
		$this->template->query = $query;
		$this->template->seoText = $seoText;
		$this->template->clickedCategory = $this->menuRepository->getMenuEntityByOrder($id, $lang);
		$articles = $this->articleRepository->findActiveArticlesInLangCategory(
			$lang,
			$categories,
			$query,
			null,
			$paginator->getLength(),
			$paginator->getOffset()
		);
		$this->template->articles = $articles;

		if ($query != null) {
			$this['fulltextSearchForm']['search']->setDefaultValue($query);
		}
	}

	public function createComponentFulltextSearchForm() {
		$form = $this->fulltextSearchForm->create();
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
		$this->redirect("category", $this->langRepository->getCurrentLang($this->session), $order, $seoText, $page, $values['search']);
	}

	/**
	 * @param string $lang
	 * @param string $from format d.m.Y
	 * @param string [$to] format d.m.Y
	 */
	public function actionSearchDate($lang, $from, $to = "") {
		dump($lang, $from,$to);	// TODO
		$this->terminate();
	}
}