<?php

namespace App\FrontendModule\Presenters;

use App\Model\Entity\ArticleEntity;
use App\Model\EnumerationRepository;
use Nette\Utils\Paginator;

class ShowPresenter extends BasePresenter {

	const ARTICLE_DETAILS_PAGER = 10;

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

	public function actionEventOnPlace($lang, $id, $seoText) {

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