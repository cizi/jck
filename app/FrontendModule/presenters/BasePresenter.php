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
use App\Model\Entity\BlockContentEntity;
use App\Model\EnumerationRepository;
use App\Model\LangRepository;
use App\Model\MenuRepository;
use App\Model\SliderPicRepository;
use App\Model\SliderSettingRepository;
use App\Model\WebconfigRepository;
use Nette\Application\UI\Presenter;
use Nette\Forms\Form;

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
		MainPageSearchForm $mainPageSearchForm
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
		$this->loadHeaderConfig();
		$this->loadLanguageStrap();
		$this->loadSliderConfig();
		$this->loadFooterConfig();

		$this->template->currentLang = $lang;
		$this->template->menuHtml = $this->menuController->renderMenuInFrontend($lang);
		$this->template->contactFormId = BlockContentPresenter::CONTACT_FORM_ID_AS_BLOCK;
		$this->template->currentUrl = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

		$this->template->wallpaperBanner = $this->bannerRepository->getBannerByType(EnumerationRepository::TYP_BANNERU_WALLPAPER);
		$this->template->fullBanner = $this->bannerRepository->getBannerByType(EnumerationRepository::TYP_BANNERU_FULL_BANNER);
		$this->template->largeRectangle = $this->bannerRepository->getBannerByType(EnumerationRepository::TYP_BANNERU_LARGE_RECTANGLE);
		$this->template->middleRectangle = $this->bannerRepository->getBannerByType(EnumerationRepository::TYP_BANNERU_MIDDLE_RECTANGLE);

		$this->template->articleRepo = $this->articleRepository;
		$this->template->enumRepo = $this->enumerationRepository;
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
	private function loadHeaderConfig() {
		// language free
		$langCommon = WebconfigRepository::KEY_LANG_FOR_COMMON;
		$this->template->showHeader = $showHeader = ($this->webconfigRepository->getByKey(WebconfigRepository::KEY_SHOW_HEADER, $langCommon) == 1 ? true : false);
		if ($showHeader) {
			$widthEnum = new WebWidthEnum();

			$this->template->headerBg = $this->webconfigRepository->getByKey(WebconfigRepository::KEY_HEADER_BACKGROUND_COLOR, $langCommon);
			$this->template->headerColor = $this->webconfigRepository->getByKey(WebconfigRepository::KEY_HEADER_COLOR, $langCommon);
			$this->template->headerWidth = $widthEnum->getValueByKey($this->webconfigRepository->getByKey(WebconfigRepository::KEY_HEADER_WIDTH, $langCommon));
			$this->template->headerHeight = (int)$this->webconfigRepository->getByKey(WebconfigRepository::KEY_HEADER_HEIGHT, $langCommon);

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
			$this->template->sliderPics = $this->bannerRepository->findBannersByType(EnumerationRepository::TYP_BANNERU_BIG_BANNER);

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
		if ($contactFormInFooter) {
			$this->template->contactFormHeader = $this->webconfigRepository->getByKey(WebconfigRepository::KEY_CONTACT_FORM_TITLE, $this->langRepository->getCurrentLang($this->session));
			$this->template->contactFormContent = $this->webconfigRepository->getByKey(WebconfigRepository::KEY_CONTACT_FORM_CONTENT, $this->langRepository->getCurrentLang($this->session));
			$this->template->contactFormBackground = $this->webconfigRepository->getByKey(WebconfigRepository::KEY_CONTACT_FORM_BACKGROUND_COLOR, $langCommon);
			$this->template->contactFormColor = $this->webconfigRepository->getByKey(WebconfigRepository::KEY_CONTACT_FORM_COLOR, $langCommon);

			$allowAttachment = $this->webconfigRepository->getByKey(WebconfigRepository::KEY_CONTACT_FORM_ATTACHMENT, $langCommon);
			$this->template->allowAttachment =  ($allowAttachment == "1" ? true : false);
		}
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
		$form->onSuccess[] = $this->contactFormSubmitted;
		return $form;
	}

	/**
	 * @return Form
	 */
	protected function createComponentSearchForm() {
		$form = $this->searchForm->create($this->langRepository->getCurrentLang($this->session));
		$form->onSuccess = $this->searchFormSubmit;

		return $form;
	}

	/**
	 * @param Form $form
	 * @param $values
	 */
	public function searchFormSubmit(Form $form, $values) {

	}
}
