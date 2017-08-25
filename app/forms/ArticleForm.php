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
		MenuController $menuController,
		PicRepository $picRepository
	) {
		$this->factory = $factory;
		$this->langRepository = $langRepository;
		$this->enumerationRepository = $enumerationRepository;
		$this->menuController = $menuController;
		$this->picRepository = $picRepository;
	}

	/**
	 * @return Form
	 */
	public function create($currentLang) {
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
		$form->addSelect("menu_order", ARTICLE_CATEGORY, $menuCategories)
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

		$picsSelect = $this->picRepository->loadForSelect();
		$form->addSelect("pic_id", ARTICLE_MAIN_PIC, $picsSelect)
			->setAttribute("class", "form-control")
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

		/* $widthSelect = new WebWidthEnum();
		$defaultValue = $widthSelect->arrayKeyValue();
		end($defaultValue);
		$form->addSelect(, BLOCK_SETTING_WIDTH, $widthSelect->arrayKeyValue())
			->setAttribute("class", "form-control menuItem")
			->setAttribute("tabindex", "1")
			->setDefaultValue(key($defaultValue));

		$form->addText(, BLOCK_SETTING_ITEM_CONTENT_COLOR)
			->setAttribute("id", "footerBackgroundColor")
			->setAttribute("class", "form-control minicolors-input")
			->setAttribute("tabindex", "2");

		$form->addText(, BLOCK_SETTING_ITEM_CONTENT_BG_COLOR)
			->setAttribute("id", "footerColor")
			->setAttribute("class", "form-control minicolors-input")
			->setAttribute("tabindex", "3"); */

		$languages = $this->langRepository->findLanguages();
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
		}

		$form->addMultiUpload("pics")
			->setAttribute("class", "form-control menuItem")
			->setAttribute("tabindex", $i+1);

		$form->addHidden("id");
		$form->addHidden("active");
		$form->addHidden("views_count");

		$form->addSubmit("confirm", ARTICLE_CONTENT_CONFIRM)
			->setAttribute("class","btn btn-primary menuItem alignRight")
			->setAttribute("tabindex", $i+1);

		return $form;
	}

}