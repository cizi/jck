<?php

namespace App\Forms;

use App\Controller\MenuController;
use App\Model\EnumerationRepository;
use Nette;
use Nette\Application\UI\Form;

class ArticleFilterForm extends Nette\Object {

	/** @var FormFactory */
	private $factory;

	/** @var EnumerationRepository */
	private $enumerationRepository;

	/** @var MenuController */
	private $menuController;

	/**
	 * ArticleFilterForm constructor.
	 * @param FormFactory $factory
	 * @param EnumerationRepository $enumerationRepository
	 * @param MenuController $menuController
	 */
	public function __construct(
		FormFactory $factory,
		EnumerationRepository $enumerationRepository,
		MenuController $menuController
	) {
		$this->factory = $factory;
		$this->enumerationRepository = $enumerationRepository;
		$this->menuController = $menuController;
	}

	/**
	 * @return Form
	 */
	public function create($currentLang) {
		$form = $this->factory->create();

		$i = 0;
		$types = $this->enumerationRepository->findEnumItemsForSelectWithEmpty($currentLang, EnumerationRepository::TYP_PRISPEVKU);
		$form->addSelect("type", ARTICLE_TYPE, $types)
			->setAttribute("class", "form-control")
			->setAttribute("tabindex", $i+1);

		$validities = $this->enumerationRepository->findEnumItemsForSelectWithEmpty($currentLang, EnumerationRepository::VALIDITA_PRISPEVKU);
		$form->addSelect("validity", ARTICLE_VALIDITY, $validities)
			->setAttribute("class", "form-control")
			->setAttribute("tabindex", $i+1);

		$menuCategories = $this->menuController->findMenuCategoriesForSelect($currentLang, true);
		$form->addMultiSelect("menuOrders", ARTICLE_CATEGORY, $menuCategories)
			->setAttribute("class", "form-control")
			->setAttribute("tabindex", $i+1);

		$sublocations = $this->enumerationRepository->findEnumItemsForSelectWithEmpty($currentLang, EnumerationRepository::SUBLOKACE);
		$form->addSelect("sublocation", ARTICLE_SUBLOCATION, $sublocations)
			->setAttribute("class", "form-control")
			->setAttribute("tabindex", $i+1);

		$places = $validities = $this->enumerationRepository->findEnumItemsForSelectWithEmpty($currentLang, EnumerationRepository::MISTO);
		$form->addSelect("place", ARTICLE_PLACE, $places)
			->setAttribute("class", "form-control")
			->setAttribute("tabindex", $i+1);

		$activityType = [ "0" => ARTICLE_ACTIVITY_BOTH, "1" => ARTICLE_ACTIVITY_ACTIVE, "2" => ARTICLE_ACTIVITY_INACTIVE];
		$form->addSelect("active", " " . ARTICLE_ACTIVE, $activityType)
			->setAttribute("class", "form-control")
			->setAttribute("tabindex", $i+1);

		$form->addSubmit("confirm", ARTICLE_TRIGGER_FILTER)
			->setAttribute("class","btn btn-primary menuItem alignRight")
			->setAttribute("onclick", "tinyMCE.triggerSave();")
			->setAttribute("tabindex", $i+1);

		return $form;
	}
}