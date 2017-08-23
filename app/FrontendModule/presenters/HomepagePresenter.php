<?php

namespace App\FrontendModule\Presenters;

use App\AdminModule\Presenters\BlockContentPresenter;
use App\Controller\FileController;
use App\Controller\MenuController;
use App\Forms\ContactForm;
use App\Model\BlockRepository;
use App\Model\Entity\BlockContentEntity;
use App\Model\Entity\MenuEntity;
use App\Model\LangRepository;
use App\Model\MenuRepository;
use Nette;
use App\Enum\WebWidthEnum;
use App\Model\SliderSettingRepository;
use App\Model\SliderPicRepository;
use App\Model\WebconfigRepository;
use App\FrontendModule\Presenters;
use Nette\Http\FileUpload;

class HomepagePresenter extends BasePresenter {

	/** @var WebconfigRepository */
	private $webconfigRepository;

	/** @var SliderSettingRepository */
	private $sliderSettingRepository;

	/** var SliderPicRepository */
	private $sliderPicRepository;

	/** @var ContactForm */
	private $contactForm;

	/** @var MenuController */
	private $menuController;

	/** @var MenuRepository */
	private $menuRepository;

	/** @var FileController */
	private $fileController;

	/** @var BlockRepository */
	private $blockRepository;

	/** @var LangRepository */
	private $langRepository;

	public function __construct(
		WebconfigRepository $webconfigRepository,
		SliderSettingRepository $sliderSettingRepository,
		SliderPicRepository $sliderPicRepository,
		ContactForm $contactForm,
		MenuController $menuController,
		MenuRepository $menuRepository,
		FileController $fileController,
		BlockRepository $blockRepository,
		LangRepository $langRepository
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
	}

	/**
	 * data init for settings values in template
	 */
	public function startup() {
		parent::startup();
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
	}

	/**
	 * @param string $lang
	 * @param string $id
	 */
	public function renderDefault($lang, $id) {
		if (empty($lang)) {
			$lang = $this->langRepository->getCurrentLang($this->session);
			$this->redirect("default", [ 'lang' => $lang, 'id' => $id]);
		}

		$userBlocks = [];
		$availableLangs = $this->langRepository->findLanguages();
		// what if link will have the same shortcut like language
		if (isset($availableLangs[$lang]) && ($lang != $this->langRepository->getCurrentLang($this->session))) {
			$this->langRepository->switchToLanguage($this->session, $lang);
			$this->redirect("default", [ 'lang' => $lang, 'id' => $id ]);
		} else {
			if ((empty($id) || ($id == "")) && !empty($lang) && (!isset($availableLangs[$lang]))) {
				$id = $lang;
			}
			if (empty($id) || ($id == "")) {    // try to find default
				$userBlocks[] = $this->getDefaultBlock();
			} else {
				$userBlocks = $this->blockRepository->findAddedBlockFronted($id,
					$this->langRepository->getCurrentLang($this->session));
				if (empty($userBlocks)) {
					$userBlocks[] = $this->getDefaultBlock();
				}
			}
			// because of sitemap.xml
			$allWebLinks = $this->menuRepository->findAllItems();
			$this->template->webAvailebleLangs = $availableLangs;
			$this->template->availableLinks = $allWebLinks;
			/** @var MenuEntity $menuLink */
			foreach($allWebLinks as $menuLink) {
				if ($menuLink->getLink() == $id) {
					$this->template->currentLink = $menuLink;
				}
}			}

			$this->template->userBlocks = $userBlocks;
			$this->template->widthEnum = new WebWidthEnum();
	}

	/**
	 * Proceed contact form
	 *
	 * @param Nette\Application\UI\Form $form
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
		$this->redirect("default");
	}

	public function createComponentContactForm() {
		$form = $this->contactForm->create();
		if ($this->webconfigRepository->getByKey(WebconfigRepository::KEY_CONTACT_FORM_RECIPIENT, WebconfigRepository::KEY_LANG_FOR_COMMON) == "") {
			$form["confirm"]->setDisabled();
		}
		$form->onSuccess[] = $this->contactFormSubmitted;
		return $form;
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
			$this->template->sliderPics = $this->sliderPicRepository->findPics();

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
	private function getDefaultBlock() {
		$id = $this->webconfigRepository->getByKey(WebconfigRepository::KEY_WEB_HOME_BLOCK,
			WebconfigRepository::KEY_LANG_FOR_COMMON);

		$blockContentEntity = new BlockContentEntity();
		if (!empty($id)) {
			$blockContentEntity = $this->blockRepository->getBlockById($this->langRepository->getCurrentLang($this->session), $id);
		}

		return $blockContentEntity;
	}
}
