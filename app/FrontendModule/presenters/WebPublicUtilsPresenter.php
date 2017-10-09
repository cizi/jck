<?php

namespace App\FrontendModule\Presenters;

use App\FrontendModule\Presenters\BasePresenter;
use App\Model\Entity\ArticleEntity;
use App\Model\Entity\MenuEntity;
use App\Model\EnumerationRepository;
use App\Model\MenuRepository;
use Nette\Utils\Strings;

class WebPublicUtilsPresenter extends BasePresenter {

	/**
	 * generates sitemap.xml in the root of the web
	 *
	 * @param string|false $isRobot
	 * for robot: http://localhost/tynim/www/admin/web-public-utils/generate-site-map/true
	 */
	public function actionGenerateSiteMap($lang) {
		$categories = $this->menuRepository->findAllCategoryOrders();	// hledám ve všech kategoriích
		$allActiveArticles = $allValidArticle = $this->articleRepository->findActiveArticlesInLangCategory($lang, $categories);
		if (count($allActiveArticles)) {
			$fileContent = [];
			$fileContent[] = '<?xml version="1.0" encoding="UTF-8"?>';
			$fileContent[] = '<urlset xmlns:xhtml="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">';
			$base = $this->getHttpRequest()->getUrl()->getBaseUrl();
			$availableLangs = $this->langRepository->findLanguages();
			/** @var ArticleEntity $article */
			foreach($allActiveArticles as $article) {
				switch ($article->getType()) {
					case EnumerationRepository::TYP_PRISPEVKU_AKCE_ORDER:
					$target = "event";
					break;

					case EnumerationRepository::TYP_PRISPEVKU_CLANEK_ORDER:
					$target = "detail";
					break;

					case EnumerationRepository::TYP_PRISPEVKU_MISTO_ORDER:
					$target = "place";
					break;
				}

				if (isset($target)) {
					$fileContent[] = '<url>';
					foreach ($availableLangs as $currentLang) {
						$contents = $article->getContents();
						if (isset($contents[$currentLang])) {
							$webalizeTitle = Strings::webalize($contents[$currentLang]->getHeader());
						} else {
							$webalizeTitle = "";
						}

						if ($currentLang == $lang) {
							$fileContent[] = '<loc>' . $base . $currentLang . '/show/' . $target  .'/'. $article->getId() . '/' . $webalizeTitle .  '</loc>';
						} else {
							$fileContent[] = '<xhtml:link rel="alternate" hreflang="' . $base . $currentLang . '/show/' . $target  .'/'. $article->getId() . '/' . $webalizeTitle . '" />';
						}
					}
					$fileContent[] = '</url>';
				}
			}

			$fileContent[] = '</urlset>';
			file_put_contents(SITEMAP_PATH . 'sitemap.xml', $fileContent);
		}
		$this->terminate();
	}
}