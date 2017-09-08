<?php

namespace App\FrontendModule\Presenters;

use App\Model\BannerRepository;

class ShowPresenter extends BasePresenter {

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
	public function actionArticle($lang, $id) {
		$article = $this->articleRepository->getArticle($id);
		if ($article != null) {
			$this->articleRepository->articleClicked($article->getId());
			$this->redirect("alekam?", $lang, $article->getId()); // todo
			/* if (($article->getUrl() != null) && ($article->getUrl() != "")) {
				$httpResponse = $this->getHttpResponse();
				$httpResponse->redirect($article->getUrl());
				$this->terminate();
			} */
		}
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