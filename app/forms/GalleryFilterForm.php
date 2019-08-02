<?php

namespace App\Forms;

use App\Controller\MenuController;
use App\Model\EnumerationRepository;
use Nette;
use Nette\Application\UI\Form;

class GalleryFilterForm {

    use Nette\SmartObject;

	/** @var FormFactory */
	private $factory;

	/**
	 * GalleryFilterForm constructor.
	 * @param FormFactory $factory
	 */
	public function __construct(
		FormFactory $factory
	) {
		$this->factory = $factory;
	}

	/**
	 * @return Form
	 */
	public function create() {
		$form = $this->factory->create();

		$i = 0;
		$form->addText("fulltext", MAIN_PAGE_SEARCH)
			->setAttribute("class", "form-control")
			->setAttribute("placeholder", MAIN_PAGE_SEARCH)
			->setAttribute("tabindex", $i+1);

		$form->addSubmit("confirm", ARTICLE_TRIGGER_FILTER)
			->setAttribute("class","btn btn-primary menuItem")
			->setAttribute("style", "margin-top: 0")
			->setAttribute("onclick", "tinyMCE.triggerSave();")
			->setAttribute("tabindex", $i+1);

		return $form;
	}
}