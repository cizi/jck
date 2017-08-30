<?php

namespace App\Forms;

use App\Controller\MenuController;
use App\Enum\WebWidthEnum;
use App\Model\BlockRepository;
use App\Model\EnumerationRepository;
use App\Model\LangRepository;
use App\Model\PicRepository;
use Nette;
use Nette\Application\UI\Form;

class BannerForm extends Nette\Object {

	/** @var FormFactory */
	private $factory;

	/** @var LangRepository */
	private $langRepository;

	/** @var EnumerationRepository */
	private $enumerationRepository;

	/** @var MenuController */
	private $menuController;

	/**
	 * ArticleForm constructor.
	 * @param FormFactory $factory
	 * @param LangRepository $langRepository
	 * @param EnumerationRepository $enumerationRepository
	 * @param MenuController $menuController
	 * @param PicRepository $picRepository
	 */
	public function __construct(
		FormFactory $factory,
		LangRepository $langRepository,
		EnumerationRepository $enumerationRepository,
		MenuController $menuController
	) {
		$this->factory = $factory;
		$this->langRepository = $langRepository;
		$this->enumerationRepository = $enumerationRepository;
		$this->menuController = $menuController;
	}

	/**
	 * @return Form
	 */
	public function create($currentLang) {
		$form = $this->factory->create();
		$form->getElementPrototype()->addAttributes(["onsubmit" => "return requiredFields();"]);

		$i = 0;
		$form->addText("title", BANNER_TITLE_ITEM)
			->setAttribute("placeholder", BANNER_TITLE_ITEM)
			->setAttribute("class", "form-control tinym_required_field")
			->setAttribute("validation", BANNER_TITLE_ITEM_REQ)
			->setAttribute("tabindex", $i+1);

		$form->addUpload("imgSelector", BANNER_IMAGE)
			->setAttribute("class", "form-control")
			->setAttribute("tabindex", $i+1);

		$form->addText("url", BANNER_ACTION_URL)
			->setAttribute("placeholder", BANNER_ACTION_URL)
			->setAttribute("class", "form-control")
			->setAttribute("tabindex", $i+1);

		$bannerTypes = $this->enumerationRepository->findEnumItemsForSelect($currentLang, EnumerationRepository::TYP_BANERU);
		$form->addSelect("banner_type", BANNER_TYPE, $bannerTypes)
			->setAttribute("class", "form-control")
			->setAttribute("tabindex", $i+1);

		$menuCategories = $this->menuController->findMenuCategoriesForSelect($currentLang);
		$form->addMultiSelect("bannerCategories", BANNER_CATEGORIES, $menuCategories)
			->setAttribute("class", "form-control")
			->setAttribute("tabindex", $i+1);

		$form->addText("date_start", BANNER_START_DATE)
			->setAttribute("class", "form-control tinym_required_field")
			->setAttribute("readonly", "readonly")
			->setAttribute("validation", BANNER_START_DATE_REQ)
			->setAttribute("tabindex", $i+1);

		$form->addText("date_end", BANNER_START_DATE)
			->setAttribute("class", "form-control")
			->setAttribute("readonly", "readonly")
			->setAttribute("tabindex", $i+1);

		$form->addCheckbox("show_on_main_page", " " . BANNER_SHOW_MAIN_PAGE)
			->setAttribute("class","activeToggleEvent")
			->setAttribute("data-toggle","toggle")
			->setAttribute("data-height","25")
			->setAttribute("data-width","50")
			->setDefaultValue(true)
			->setAttribute("tabindex", $i+1);

		/* $languages = $this->langRepository->findLanguages();
		foreach ($languages as $lang) {
			$container = $form->addContainer($lang);

			$container->addText("lang")
				->setAttribute("class", "form-control menuItem langDivider")
				->setAttribute("tabindex", "-1")
				->setAttribute("readonly", "readonly")
				->setValue($lang);

			$container->addText("header", ARTICLE_CONTENT_HEADER)
				->setAttribute("class", "form-control menuItem")
				->setAttribute("placeholder", ARTICLE_CONTENT_HEADER)
				->setAttribute("tabindex", $i+1);

			$container->addTextArea("content", ARTICLE_CONTENT_CONTENT)
				->setAttribute("class", "form-control menuItem mceBlockContent")
				->setAttribute("placeholder", ARTICLE_CONTENT_CONTENT)
				->setAttribute("tabindex", $i+1);

			$container->addText("seo", ARTICLE_CONTENT_SEO)
				->setAttribute("class", "form-control menuItem")
				->setAttribute("placeholder", ARTICLE_CONTENT_SEO)
				->setAttribute("tabindex", $i+1);

			$container->addHidden("article_id");
			$i++;
		} */

		$form->addHidden("id");
		$form->addHidden("show_counter");
		$form->addHidden("click_counter");
		$form->addHidden("user_id");
		$form->addHidden("img")->setAttribute("id", "bannerImg");

		$form->addSubmit("confirm", BANNER_EDIT_CONFIRM)
			->setAttribute("class","btn btn-primary menuItem alignRight")
			->setAttribute("tabindex", $i+1);

		return $form;
	}
}