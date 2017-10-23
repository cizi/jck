<?php

namespace App\Forms;

use App\Controller\MenuController;
use App\Enum\WebWidthEnum;
use App\Model\BlockRepository;
use App\Model\EnumerationRepository;
use App\Model\GalleryRepository;
use App\Model\LangRepository;
use App\Model\PicRepository;
use Nette;
use Nette\Application\UI\Form;

class ArticleForm extends Nette\Object {

	/** @var FormFactory */
	private $factory;

	/** @var LangRepository */
	private $langRepository;

	/** @var EnumerationRepository */
	private $enumerationRepository;

	/** @var MenuController */
	private $menuController;

	/** @var PicRepository */
	private $picRepository;

	/** @var GalleryRepository */
	private $galleryRepository;

	/**
	 * ArticleForm constructor.
	 * @param FormFactory $factory
	 * @param LangRepository $langRepository
	 * @param EnumerationRepository $enumerationRepository
	 * @param MenuController $menuController
	 * @param PicRepository $picRepository
	 * @param GalleryRepository $galleryRepository
	 */
	public function __construct(
		FormFactory $factory,
		LangRepository $langRepository,
		EnumerationRepository $enumerationRepository,
		MenuController $menuController,
		PicRepository $picRepository,
		GalleryRepository $galleryRepository
	) {
		$this->factory = $factory;
		$this->langRepository = $langRepository;
		$this->enumerationRepository = $enumerationRepository;
		$this->menuController = $menuController;
		$this->picRepository = $picRepository;
		$this->galleryRepository = $galleryRepository;
	}

	/**
	 * @return Form
	 */
	public function create($currentLang, $defaultTakingTimesInputs = 0) {
		$form = $this->factory->create();
		$form->getElementPrototype()->addAttributes(["onsubmit" => "return requiredFields();"]);

		$i = 0;
		$types = $this->enumerationRepository->findEnumItemsForSelect($currentLang, EnumerationRepository::TYP_PRISPEVKU);
		$form->addSelect("type", ARTICLE_TYPE, $types)
			->setAttribute("class", "form-control")
			->setAttribute("tabindex", $i+1);

		$validities = $this->enumerationRepository->findEnumItemsForSelect($currentLang, EnumerationRepository::VALIDITA_PRISPEVKU);
		$form->addSelect("validity", ARTICLE_VALIDITY, $validities)
			->setAttribute("class", "form-control")
			->setAttribute("tabindex", $i+1);

		$menuCategories = $this->menuController->findMenuCategoriesForSelect($currentLang);
		$form->addMultiSelect("menuOrders", ARTICLE_CATEGORY, $menuCategories)
			->setAttribute("class", "form-control")
			->setAttribute("tabindex", $i+1);

		$places = $validities = $this->enumerationRepository->findEnumItemsForSelectWithEmpty($currentLang, EnumerationRepository::MISTO);
		$form->addSelect("place", ARTICLE_PLACE, $places)
			->setAttribute("class", "form-control")
			->setAttribute("tabindex", $i+1);

		$form->addText("place_text", ARTICLE_PLACE_TEXT)
			->setAttribute("class", "form-control")
			->setAttribute("tabindex", $i+1);

		$form->addText("address", ARTICLE_ADDRESS)
			->setAttribute("class", "form-control")
			->setAttribute("tabindex", $i+1);

		$locations = $this->enumerationRepository->findEnumItemsForSelect($currentLang, EnumerationRepository::LOKACE);
		$form->addSelect("location", ARTICLE_LOCATION, $locations)
			->setAttribute("class", "form-control")
			->setAttribute("tabindex", $i+1);

		$sublocations = $this->enumerationRepository->findEnumItemsForSelectWithEmpty($currentLang, EnumerationRepository::SUBLOKACE);
		$form->addSelect("sublocation", ARTICLE_SUBLOCATION, $sublocations)
			->setAttribute("class", "form-control")
			->setAttribute("tabindex", $i+1);

		$form->addTextArea("contact", ARTICLE_CONTACT)
			->setAttribute("class", "form-control menuItem mceBlockContent")
			->setAttribute("placeholder", ARTICLE_CONTACT)
			->setAttribute("tabindex", $i+1);

		$form->addText("contact_email", ARTICLE_CONTACT_EMAIL)
			->setAttribute("class", "form-control tinym_required_field")
			->setAttribute("placeholder", ARTICLE_CONTACT_EMAIL)
			->setAttribute("validation", ARTICLE_CONTACT_EMAIL_REQ)
			->addRule(Form::EMAIL, ARTICLE_CONTACT_EMAIL_FORMAT)
			->setAttribute("tabindex", $i+1);

		$form->addCheckbox("active", " " . ARTICLE_ACTIVE)
			->setAttribute("class","activeToggleEvent")
			->setAttribute("data-toggle","toggle")
			->setAttribute("data-height","25")
			->setAttribute("data-width","50")
			->setDefaultValue(true)
			->setAttribute("tabindex", $i+1);

		$form->addText("url", ARTICLE_URL)
			->setAttribute("placeholder", ARTICLE_URL)
			->setAttribute("class", "form-control")
			->setAttribute("tabindex", $i+1);

		$form->addText("fb_url", ARTICLE_FB_URL)
			->setAttribute("placeholder", ARTICLE_FB_URL)
			->setAttribute("class", "form-control")
			->setAttribute("tabindex", $i+1);

		$form->addText("yt_url", ARTICLE_YT_URL)
			->setAttribute("placeholder", ARTICLE_YT_URL)
			->setAttribute("class", "form-control")
			->setAttribute("tabindex", $i+1);

		$languages = $this->langRepository->findLanguages();
		foreach ($languages as $lang) {
			$container = $form->addContainer($lang);

			$container->addText("lang")
				->setAttribute("class", "form-control menuItem langDivider")
				->setAttribute("tabindex", "-1")
				->setAttribute("readonly", "readonly")
				->setValue($lang);

			$container->addText("header", ARTICLE_CONTENT_HEADER)
				->setAttribute("class", "form-control menuItem tinym_required_field")
				->setAttribute("validation", ARTICLE_CONTENT_HEADER_REQ)
				->setAttribute("placeholder", ARTICLE_CONTENT_HEADER)
				->setAttribute("tabindex", $i+1);

			$container->addTextArea("content", ARTICLE_CONTENT_CONTENT)
				->setAttribute("class", "form-control menuItem mceBlockContent tinym_required_field")
				->setAttribute("validation",ARTICLE_CONTENT_CONTENT_REQ)
				->setAttribute("placeholder", ARTICLE_CONTENT_CONTENT)
				->setAttribute("id", "article_content_".$lang)
				->setAttribute("tabindex", $i+1);

			$container->addHidden("article_id");
			$i++;
		}
		$form->addButton("rewriteCsToEn", ARTICLE_CONTENT_REWRITE)
			->setAttribute("class","btn btn-primary menuItem alignRight")
			->setAttribute("onclick", "rewriteContent();")
			->setAttribute("tabindex", $i+1);

		// name, factory, default count
		$calendar = $form->addContainer("calendar");
		$calendarReplicator = $calendar->addDynamic('calendar', function (Nette\Forms\Container $container) {
			$container->addHidden("id");
			$container->addHidden("article_id");
			$container->addText('date_from', ARTICLE_DATE_FROM)
				->setAttribute("class", "form-control menuItem takingDate tinym_required_field")
				->setAttribute("readonly", "readonly")
				->setAttribute("validation", ARTICLE_TIMETABLE_START_DATE_MISSING);;

			$container->addText('date_to', ARTICLE_DATE_TO)
				->setAttribute("class", "form-control menuItem takingDate")
				->setAttribute("readonly", "readonly");

			$container->addText('time', ARTICLE_START_TIME)
				->setAttribute("class", "form-control menuItem takingTime");// tinym_required_field")
				//->setAttribute("validation", ARTICLE_TIMETABLE_TIME_WRONG_FORMAT);
				//->setAttribute("readonly", "readonly");

			$container->addSubmit('removeTakingTime', ARTICLE_REMOVE_TIMETABLE)
				->setAttribute("class", "btn btn-danger menuItem")
				->setAttribute("onclick", "articleRemoveRequiredFields();")
				->addRemoveOnClick();
		}, $defaultTakingTimesInputs);

		$calendarReplicator->addSubmit('addTakingTime', ARTICLE_ADD_TIMETABLE)
			->setAttribute("class", "btn btn-primary menuItem")
			->setAttribute("id", "addNextTakingTime")
			->setAttribute("onclick", "articleRemoveRequiredFields(); tinyMCE.triggerSave();")
			->addCreateOnClick(true);

		$form->addHidden("id");
		$form->addHidden("show_counter");
		$form->addHidden("click_counter");
		$form->addHidden("inserted_timestamp");

		$form->addUpload("picUrlUpload", ARTICLE_MAIN_URL)
			->setAttribute("class", "form-control")
			->setAttribute("tabindex", $i+1);
		$form->addHidden("pic_url")->setAttribute("id", "articleMainImgUrl");

		$galls = $this->galleryRepository->findGalleriesInLang($currentLang);
		$galleries[0] = EnumerationRepository::NOT_SELECTED;
		foreach ($galls as $gal) {
			$galleries[$gal->getId()] = $gal->getContents()[$currentLang]->getHeader();
		}
		$form->addSelect("gallery_id", ARTICLE_GALLERY, $galleries)
			->setAttribute("class", "form-control")
			->setAttribute("tabindex", $i+1);

		$form->addMultiUpload("docsUpload", ARTICLE_MAIN_DOCS)
			->setAttribute("class", "form-control")
			->setAttribute("tabindex", $i+1);

		$picsSelect = $this->picRepository->loadForSelect();
		$form->addSelect("pic_id", ARTICLE_MAIN_PIC, $picsSelect)
			->setAttribute("class", "form-control")
			->setAttribute("tabindex", $i+1);

		$form->addSubmit("confirm", ARTICLE_CONTENT_CONFIRM)
			->setAttribute("class","btn btn-primary menuItem alignRight confirmButton")
			->setAttribute("onclick", "submitArticleForm(event); tinyMCE.triggerSave();")
			->setAttribute("tabindex", $i+1);

		return $form;
	}
}