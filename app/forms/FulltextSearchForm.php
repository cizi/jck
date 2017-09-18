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

class FulltextSearchForm extends Nette\Object {

	/** @var FormFactory */
	private $factory;

	/** @var LangRepository */
	private $langRepository;

	/** @var MenuController */
	private $menuController;

	/** @var PicRepository */
	private $picRepository;

	/**
	 * ArticleForm constructor.
	 * @param FormFactory $factory
	 * @param LangRepository $langRepository
	 * @param MenuController $menuController
	 * @param PicRepository $picRepository
	 */
	public function __construct(
		FormFactory $factory,
		LangRepository $langRepository,
		MenuController $menuController,
		PicRepository $picRepository
	) {
		$this->factory = $factory;
		$this->langRepository = $langRepository;
		$this->menuController = $menuController;
		$this->picRepository = $picRepository;
	}

	/**
	 * @return Form
	 */
	public function create() {
		$form = $this->factory->create();
		$form->getElementPrototype()->addAttributes(["onsubmit" => "return requiredFields();"]);

		$i = 0;
		$form->addText("search", MAIN_PAGE_SEARCH)
			->setAttribute("validation", MAIN_PAGE_SEARCH_REQ)
			->setAttribute("placeholder", MAIN_PAGE_SEARCH)
			->setAttribute("class", "form-control tinym_required_field input-sm")
			->setAttribute("tabindex", $i+1);

		$form->addSubmit("confirm", MAIN_PAGE_SEARCH)
			->setAttribute("class","btn btn-primary btn-sm")
			->setAttribute("tabindex", $i+1);

		return $form;
	}
}