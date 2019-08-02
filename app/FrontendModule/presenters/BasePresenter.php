<?php

namespace App\FrontendModule\Presenters;

use App\AdminModule\Presenters\BlockContentPresenter;
use App\Controller\FileController;
use App\Controller\MenuController;
use App\Enum\WebWidthEnum;
use App\Forms\ContactForm;
use App\Forms\MainPageSearchForm;
use App\Forms\SearchForm;
use App\Model\ArticleRepository;
use App\Model\BannerRepository;
use App\Model\BlockRepository;
use App\Model\Entity\ArticleCategoryEntity;
use App\Model\Entity\BlockContentEntity;
use App\Model\EnumerationRepository;
use App\Model\GalleryRepository;
use App\Model\LangRepository;
use App\Model\MenuRepository;
use App\Model\PicRepository;
use App\Model\SliderPicRepository;
use App\Model\SliderSettingRepository;
use App\Model\WebconfigRepository;
use Nette\Application\UI\Presenter;
use Nette\Forms\Form;
use Nette\Http\FileUpload;

/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends Presenter {

	/** @var WebconfigRepository */
	protected $webconfigRepository;

	/** @var SliderSettingRepository */
	protected $sliderSettingRepository;

	/** var SliderPicRepository */
	protected $sliderPicRepository;

	/** @var ContactForm */
	protected $contactForm;

	/** @var MenuController */
	protected $menuController;

	/** @var MenuRepository */
	protected $menuRepository;

	/** @var FileController */
	protected $fileController;

	/** @var BlockRepository */
	protected $blockRepository;

	/** @var SearchForm */
	protected $searchForm;

	/** @var BannerRepository */
	protected $bannerRepository;

	/** @var ArticleRepository */
	protected $articleRepository;

	/** @var EnumerationRepository */
	protected $enumerationRepository;

	/** @var MainPageSearchForm  */
	protected $mainPageSearchForm;

	/** @var LangRepository */
	protected $langRepository;

	/** @var PicRepository */
	protected $picRepository;

	/** @var GalleryRepository */
	protected $galleryRepository;

	public function injectBase(
		WebconfigRepository $webconfigRepository,
		SliderSettingRepository $sliderSettingRepository,
		SliderPicRepository $sliderPicRepository,
		ContactForm $contactForm,
		MenuController $menuController,
		MenuRepository $menuRepository,
		FileController $fileController,
		BlockRepository $blockRepository,
		LangRepository $langRepository,
		SearchForm $searchForm,
		BannerRepository $bannerRepository,
		ArticleRepository $articleRepository,
		EnumerationRepository $enumerationRepository,
		MainPageSearchForm $mainPageSearchForm,
		PicRepository $picRepository,
		GalleryRepository $galleryRepository
	) {
		$this->webconfigRepository = $webconfigRepository;
		$this->sliderSettingRepository = $sliderSettingRepository;
		$this->sliderPicRepository = $sliderPicRepository;
		$this->contactForm = $contactForm;
		$this->menuController = $menuController;
		$this->menuRepository = $menuRepository;
		$this->fileController = $fileController;
		$this->blockRepository = $blockRepository;
		$this->langRepository = $langRepository;
		$this->searchForm = $searchForm;
		$this->bannerRepository = $bannerRepository;
		$this->articleRepository = $articleRepository;
		$this->enumerationRepository = $enumerationRepository;
		$this->mainPageSearchForm = $mainPageSearchForm;
		$this->picRepository = $picRepository;
		$this->galleryRepository = $galleryRepository;
	}

	public function startup() {
		parent::startup();

		// language setting
		$lang = $this->langRepository->getCurrentLang($this->session);
		if (!isset($lang) || $lang == "") {
			$lang = $this->context->parameters['language']['default'];
			$this->langRepository->switchToLanguage($this->session, $lang);
		}
		$this->langRepository->loadLanguageMutation($lang);
		$lang = $this->langRepository->getCurrentLang($this->session);

		// load another page settings
		$this->loadWebConfig($lang);
		$this->loadHeaderConfig($lang);
		$this->loadLanguageStrap();
		$this->loadSliderConfig();
		$this->loadFooterConfig();

		$availableLangs = $this->langRepository->findLanguages();
		$this->template->requestedAction = $this->getPresenter()->getAction();
		$this->template->webAvailebleLangs = $availableLangs;
		$this->template->currentLang = $lang;
		$this->template->menuHtml = $this->menuController->renderMenuInFrontend($lang, $this->presenter);
		$this->template->contactFormId = BlockContentPresenter::CONTACT_FORM_ID_AS_BLOCK;
		$this->template->currentUrl = "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

		$this->template->eventOrder = EnumerationRepository::TYP_PRISPEVKU_AKCE_ORDER;
		$this->template->textArticleOrder = EnumerationRepository::TYP_PRISPEVKU_CLANEK_ORDER;
		$this->template->placeOrder = EnumerationRepository::TYP_PRISPEVKU_MISTO_ORDER;

		$this->template->articleRepo = $this->articleRepository;
		$this->template->enumRepo = $this->enumerationRepository;
		$this->template->picRepo = $this->picRepository;
		$this->template->months = [JANUARY, FEBRUARY, MARCH, APRIL, MAY, JUNE, JULY, AUGUST, SEPTEMBER, OCTOBER, NOVEMBER, DEMEBER];
		$this->template->days = [MON, THU, WED, THR, FRI, SAT, SUN];
		$this->template->minDate = \DateTime::createFromFormat(ArticleRepository::DB_DATETIME_MASK, '1970-01-01 00:00:00');
	}

	public function checkLanguage($lang) {
		$availableLangs = $this->langRepository->findLanguages();
		if (empty($lang) || (isset($availableLangs[$lang]) == false)) {
			$lang = $this->langRepository->getCurrentLang($this->session);
			$this->redirect("Homepage:Default", [ 'lang' => $lang]);
		}
		if (isset($availableLangs[$lang]) && ($lang != $this->langRepository->getCurrentLang($this->session))) {
			$this->langRepository->switchToLanguage($this->session, $lang);
			$redir = $this->getHttpRequest()->getUrl() . "";
			$httpResponse = $this->getHttpResponse();
			$httpResponse->redirect($redir);
			//$this->redirect($this->getHttpRequest()->getUrl()->getAbsoluteUrl(), [ 'lang' => $lang, 'id' => $id ]);
		}
	}

	/**
	 * @param array $categories
	 */
	protected function createArticleCategoryArrayByMenuOrder(array $categories) {
		$articlesCategories = [];
		foreach ($categories as $category) {
			$articleCategoryEntity = new ArticleCategoryEntity();
			$articleCategoryEntity->setMenuOrder($category);
			$articlesCategories[] = $articleCategoryEntity;
		}

		return $articlesCategories;
	}

	/**
	 * It loads config from admin to page
	 */
	private function loadWebConfig($lang) {
		// depending on language
		$this->template->title = $this->webconfigRepository->getByKey(WebconfigRepository::KEY_WEB_TITLE, $lang);
		$this->template->googleAnalytics = $this->webconfigRepository->getByKey(WebconfigRepository::KEY_WEB_GOOGLE_ANALYTICS, $lang);
		$this->template->webKeywords = $this->webconfigRepository->getByKey(WebconfigRepository::KEY_WEB_KEYWORDS, $lang);

		// language free
		$widthEnum = new WebWidthEnum();
		$langCommon = WebconfigRepository::KEY_LANG_FOR_COMMON;
		$this->template->favicon = $this->webconfigRepository->getByKey(WebconfigRepository::KEY_FAVICON, $langCommon);
		$this->template->bodyWidth = $widthEnum->getValueByKey($this->webconfigRepository->getByKey(WebconfigRepository::KEY_WEB_WIDTH, $langCommon));
		$this->template->bodyBackgroundColor = $this->webconfigRepository->getByKey(WebconfigRepository::KEY_BODY_BACKGROUND_COLOR, $langCommon);
		$this->template->showMenu = ($this->webconfigRepository->getByKey(WebconfigRepository::KEY_WEB_SHOW_MENU, $langCommon) == 1 ? true : false);
		$this->template->showHomeButtonInMenu = ($this->webconfigRepository->getByKey(WebconfigRepository::KEY_WEB_SHOW_HOME, $langCommon) == 1 ? true : false);
		$this->template->menuColor = $this->webconfigRepository->getByKey(WebconfigRepository::KEY_WEB_MENU_BG, $langCommon);
		$this->template->menuLinkColor = $this->webconfigRepository->getByKey(WebconfigRepository::KEY_WEB_MENU_LINK_COLOR, $langCommon);
	}

	/**
	 * Loads language strap configuration
	 */
	private function loadLanguageStrap() {
		if (count($this->langRepository->findLanguages()) > 1) {
			// language free
			$widthEnum = new WebWidthEnum();
			$langCommon = WebconfigRepository::KEY_LANG_FOR_COMMON;

			$this->template->languageStrapShow = true;
			$this->template->languageStrapWidth = $widthEnum->getValueByKey($this->webconfigRepository->getByKey(LangRepository::KEY_LANG_WIDTH, $langCommon));
			$this->template->languageStrapBgColor = $this->webconfigRepository->getByKey(LangRepository::KEY_LANG_BG_COLOR, $langCommon);
			$this->template->languageStrapFontColor = $this->webconfigRepository->getByKey(LangRepository::KEY_LANG_FONT_COLOR, $langCommon);
			$this->template->langFlagKey = LangRepository::KEY_LANG_ITEM_FLAG;
			$this->template->languageStrapLanguages = $this->langRepository->findLanguagesWithFlags();
		} else {
			$this->template->languageStrapShow = false;
		}
	}

	/**
	 * Loads configuration for static header
	 */
	private function loadHeaderConfig($lang) {
		// language free
		$langCommon = WebconfigRepository::KEY_LANG_FOR_COMMON;
		$this->template->showHeader = $showHeader = ($this->webconfigRepository->getByKey(WebconfigRepository::KEY_SHOW_HEADER, $langCommon) == 1 ? true : false);
		if ($showHeader) {
			$widthEnum = new WebWidthEnum();

			$this->template->headerBg = $this->webconfigRepository->getByKey(WebconfigRepository::KEY_HEADER_BACKGROUND_COLOR, $langCommon);
			$this->template->headerColor = $this->webconfigRepository->getByKey(WebconfigRepository::KEY_HEADER_COLOR, $langCommon);
			$this->template->headerWidth = $widthEnum->getValueByKey($this->webconfigRepository->getByKey(WebconfigRepository::KEY_HEADER_WIDTH, $langCommon));
			$this->template->headerHeight = (int)$this->webconfigRepository->getByKey(WebconfigRepository::KEY_HEADER_HEIGHT, $langCommon);

			// articles in header
			$this->template->headerArticleColor = $this->webconfigRepository->getByKey(WebconfigRepository::KEY_HEADER_ARTICLES_COLOR, $langCommon);
			$this->template->headerArticleBgColor = $this->webconfigRepository->getByKey(WebconfigRepository::KEY_HEADER_ARTICLES_BG_COLOR, $langCommon);
			$this->template->headerArticleCount = intval($this->webconfigRepository->getByKey(WebconfigRepository::KEY_HEADER_ARTICLES_COUNT, $langCommon));
			$this->template->headerArticleTiming = intval($this->webconfigRepository->getByKey(WebconfigRepository::KEY_HEADER_ARTICLES_TIMING, $langCommon));
			$this->template->headerArticleHeader = $this->webconfigRepository->getByKey(WebconfigRepository::KEY_HEADER_ARTICLES_HEADER, $lang);
			if ($this->template->headerArticleCount > 0) {
				$this->template->newestArticles = $this->articleRepository->findActiveArticlesInLang($lang, EnumerationRepository::TYP_PRISPEVKU_CLANEK_ORDER);
			}
			// img path fixing
			$headerContent = $this->webconfigRepository->getByKey(WebconfigRepository::KEY_HEADER_CONTENT, $this->langRepository->getCurrentLang($this->session));
			$this->template->headerContent = str_replace("../../upload/", "./upload/", $headerContent);
		}
	}

	/**
	 * It loads slider option to page
	 */
	private function loadSliderConfig() {
		// slider and its pics
		if ($this->sliderSettingRepository->getByKey(SliderSettingRepository::KEY_SLIDER_ON)) {
			$this->template->sliderEnabled = true;
			$widthEnum = new WebWidthEnum();
			$widthOption = $this->sliderSettingRepository->getByKey(SliderSettingRepository::KEY_SLIDER_WIDTH);
			$width = $widthEnum->getValueByKey($widthOption);
			$this->template->sliderWidth = (empty($width) ? "100%" : $width);
			$this->template->sliderSpeed = $this->sliderSettingRepository->getByKey(SliderSettingRepository::KEY_SLIDER_TIMING) * 1000;
			$this->template->slideShow = ($this->sliderSettingRepository->getByKey(SliderSettingRepository::KEY_SLIDER_SLIDE_SHOW) == "1" ? true : false);
			$this->template->sliderControls = ($this->sliderSettingRepository->getByKey(SliderSettingRepository::KEY_SLIDER_CONTROLS) == "1" ? true : false);
		} else {
			$this->template->sliderEnabled = false;
			$this->template->sliderPics = [];
		}
	}

	/**
	 * It loads info about footer
	 */
	private function loadFooterConfig() {
		$langCommon = WebconfigRepository::KEY_LANG_FOR_COMMON;
		$this->template->showFooter = $showFooter = ($this->webconfigRepository->getByKey(WebconfigRepository::KEY_SHOW_FOOTER, $langCommon) == 1 ? true : false);
		if ($showFooter) {
			$widthEnum = new WebWidthEnum();

			$this->template->footerBg = $this->webconfigRepository->getByKey(WebconfigRepository::KEY_FOOTER_BACKGROUND_COLOR, $langCommon);
			$this->template->footerColor = $this->webconfigRepository->getByKey(WebconfigRepository::KEY_FOOTER_COLOR, $langCommon);
			$this->template->footerWidth = $widthEnum->getValueByKey($this->webconfigRepository->getByKey(WebconfigRepository::KEY_FOOTER_WIDTH, $langCommon));

			// img path fixing
			$footerContent = $this->webconfigRepository->getByKey(WebconfigRepository::KEY_FOOTER_CONTENT, $this->langRepository->getCurrentLang($this->session));
			$this->template->footerContent = str_replace("../../upload/", "./upload/", $footerContent);
		}

		$contactFormInFooter = $this->webconfigRepository->getByKey(WebconfigRepository::KEY_SHOW_CONTACT_FORM_IN_FOOTER, $langCommon);
		$this->template->isContactFormInFooter = ($contactFormInFooter == "1" ? true : false);
		//if ($contactFormInFooter) {
			$this->template->contactFormHeader = $this->webconfigRepository->getByKey(WebconfigRepository::KEY_CONTACT_FORM_TITLE, $this->langRepository->getCurrentLang($this->session));
			$this->template->contactFormContent = $this->webconfigRepository->getByKey(WebconfigRepository::KEY_CONTACT_FORM_CONTENT, $this->langRepository->getCurrentLang($this->session));
			$this->template->contactFormBackground = $this->webconfigRepository->getByKey(WebconfigRepository::KEY_CONTACT_FORM_BACKGROUND_COLOR, $langCommon);
			$this->template->contactFormColor = $this->webconfigRepository->getByKey(WebconfigRepository::KEY_CONTACT_FORM_COLOR, $langCommon);

			$allowAttachment = $this->webconfigRepository->getByKey(WebconfigRepository::KEY_CONTACT_FORM_ATTACHMENT, $langCommon);
			$this->template->allowAttachment =  ($allowAttachment == "1" ? true : false);
		// }
	}

	/**
	 * returns default block
	 *
	 * @return BlockContentEntity|\App\Model\Entity\BlockEntity
	 */
	protected function getDefaultBlock() {
		$id = $this->webconfigRepository->getByKey(WebconfigRepository::KEY_WEB_HOME_BLOCK,
			WebconfigRepository::KEY_LANG_FOR_COMMON);

		$blockContentEntity = new BlockContentEntity();
		if (!empty($id)) {
			$blockContentEntity = $this->blockRepository->getBlockById($this->langRepository->getCurrentLang($this->session), $id);
		}

		return $blockContentEntity;
	}

	protected function createComponentContactForm() {
		$form = $this->contactForm->create();
		if ($this->webconfigRepository->getByKey(WebconfigRepository::KEY_CONTACT_FORM_RECIPIENT, WebconfigRepository::KEY_LANG_FOR_COMMON) == "") {
			$form["confirm"]->setDisabled();
		}
		$form->onSuccess[] = [$this, 'contactFormSubmitted'];
		return $form;
	}

	/**
	 * @return Form
	 */
	protected function createComponentSearchForm() {
		$form = $this->searchForm->create($this->langRepository->getCurrentLang($this->session));
		$form->onSuccess[] = [$this, 'searchFormSubmit'];

		return $form;
	}

	/**
	 * @param Form $form
	 * @param $values
	 */
	public function searchFormSubmit(Form $form, $values) {
		if (
			(isset($values['search']) && trim($values['search']) == "")
			&& (isset($values['destination']) && trim($values['destination']) == 0)
		) {
			$this->flashMessage(MAIN_SEARCH_REQ_FIELDS, "alert-danger");
			$this->redirect("Homepage:Default", $this->langRepository->getCurrentLang($this->session));
		} else {
			$this->redirect(
				"Show:Category",
					$this->langRepository->getCurrentLang($this->session),
					null,
					"",
					1,
					$values['search'],
					($values['destination'] != null ? $values['destination'] : null)
			);
		}
	}
	/**
	 * @return array
	 */
	public function decodeFilterFromQuery() {
		$filter = [];
		if ($this->filter != "") {
			$arr = explode("&", $this->filter);
			foreach ($arr as $filterItem) {
				$filterPiece = explode("=", $filterItem);
				if (
					(count($filterPiece) > 1)
					&& ($filterPiece[0] != "")
					&& ($filterPiece[1] != "")
					&& ($filterPiece[0] != "filter")
					&& ($filterPiece[0] != "confirm")
					&& ($filterPiece[0] != "do")
					&& ($filterPiece[1] != "0")
				) {
					if ($filterPiece[0] == "menuOrders") {
						$filter[$filterPiece[0]] = explode(",", $filterPiece[1]);
					} else {
						$filter[$filterPiece[0]] = $filterPiece[1];
					}
				}
			}
		}

		return $filter;
	}

	/**
	 * Proceed contact form
	 *
	 * @param Form $form
	 * @param $values
	 * @throws \Exception
	 * @throws \phpmailerException
	 */
	public function contactFormSubmitted($form, $values) {
		if (
			isset($values['contactEmail']) && $values['contactEmail'] != ""
			&& isset($values['name']) && $values['name'] != ""
			&& isset($values['subject']) && $values['subject'] != ""
			&& isset($values['text']) && $values['text'] != ""
		) {
			$supportedFilesFormat = ["png", "jpg", "bmp", "pdf", "doc", "xls", "docx", "xlsx"];
			$fileError = false;
			$path = "";
			if (!empty($values['attachment'])) {
				/** @var FileUpload $file */
				$file = $values['attachment'];
				if (!empty($file->name)) {
					$fileController = new FileController();
					if ($fileController->upload($file, $supportedFilesFormat, $this->getHttpRequest()->getUrl()->getBaseUrl()) == false) {
						$fileError = true;
						$this->flashMessage(CONTACT_FORM_UNSUPPORTED_FILE_FORMAT, "alert-danger");
					} else {
						$path = $fileController->getPath();
					}
				}
			}

			if ($fileError == false) {
				$email = new \PHPMailer();
				$email->CharSet = "UTF-8";
				$email->From = $values['contactEmail'];
				$email->FromName = $values['name'];
				$email->Subject = CONTACT_FORM_EMAIL_MY_SUBJECT . " - " . $values['subject'];
				$email->Body = $values['text'];
				$email->AddAddress($this->webconfigRepository->getByKey(WebconfigRepository::KEY_CONTACT_FORM_RECIPIENT, WebconfigRepository::KEY_LANG_FOR_COMMON));
				if (!empty($path)) {
					$email->AddAttachment($path);
				}
				$email->Send();
				$this->flashMessage(CONTACT_FORM_WAS_SENT, "alert-success");
			}
		} else {
			$this->flashMessage(CONTACT_FORM_SENT_FAILED, "alert-danger");
		}
		$this->redirect("default", $this->langRepository->getCurrentLang($this->session));
	}

}
