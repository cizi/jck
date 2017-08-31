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

class SearchForm extends Nette\Object {

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
		$form->addText("search", MAIN_PAGE_SEARCH)
			->setAttribute("class", "form-control input-sm")
			->setAttribute("tabindex", $i+1);

		$locations = $this->enumerationRepository->findEnumItemsForSelect($currentLang, EnumerationRepository::LOKACE);
		$form->addSelect("destination", MAIN_PAGE_DESTINATION, $locations)
			->setAttribute("class", "form-control input-sm")
			->setAttribute("tabindex", $i+1);

		$form->addSubmit("confirm", ARTICLE_CONTENT_CONFIRM)
			->setAttribute("class","btn btn-primary input-sm")
			->setAttribute("tabindex", $i+1);

		return $form;
	}
}