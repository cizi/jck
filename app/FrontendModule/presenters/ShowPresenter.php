<?php

namespace App\FrontendModule\Presenters;

use App\Model\BannerRepository;

class ShowPresenter extends BasePresenter {

	/** @var BannerRepository  */
	private $bannerRepository;

	public function __construct(BannerRepository $bannerRepository) {
		$this->bannerRepository = $bannerRepository;
	}

	/**
	 * @param string $lang
	 * @param int $id
	 */
	public function actionBanner($lang, $id) {
		$banner = $this->bannerRepository->getBanner($id);
		if ($banner != null) {
			$this->bannerRepository->bannerClicked($banner->getId());
			if (($banner->getUrl() != "") && ($banner->getUrl() != "")) {
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

	}

	/**
	 * @param string $lang
	 * @param string $from format d.m.Y
	 * @param string [$to] format d.m.Y
	 */
	public function actionSearchDate($lang, $from, $to = "") {
		dump($lang, $from,$to);
		$this->terminate();
	}
}