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

class FulltextSearchForm {

    use Nette\SmartObject;

	/** @var FormFactory */
	private $factory;

	/** @var  EnumerationRepository */
	private $enumerationRepository;

	/**
	 * FulltextSearchForm constructor.
	 * @param FormFactory $factory
	 * @param EnumerationRepository $enumerationRepository
	 */
	public function __construct(
		FormFactory $factory,
		EnumerationRepository $enumerationRepository
	) {
		$this->factory = $factory;
		$this->enumerationRepository = $enumerationRepository;
	}

	/**
	 * @return Form
	 */
	public function create($lang) {
		$form = $this->factory->create();
		$form->getElementPrototype()->addAttributes(["onsubmit" => "return requiredFields();"]);

		$i = 0;
		$form->addText("search", MAIN_PAGE_SEARCH)
			// ->setAttribute("validation", MAIN_PAGE_SEARCH_REQ)
			->setAttribute("placeholder", MAIN_PAGE_SEARCH)
			->setAttribute("class", "form-control input-sm")
			->setAttribute("tabindex", $i+1);

		$sublocationsEnum = $this->enumerationRepository->findEnumItemsForSelect($lang, EnumerationRepository::SUBLOKACE);
		$sublocations = array(
			"0" => \Nette\Utils\Html::el()->setText(ARTICLE_SUBLOCATION) //->addAttributes(["disabled" => 'disabled'])
		);
		foreach ($sublocationsEnum as $id => $val) {
			$sublocations[$id] = $val;
		}

		$form->addSelect("sublocation", ARTICLE_SUBLOCATION, $sublocations)
			->setAttribute("class", "form-control input-sm")
			->setAttribute("tabindex", $i+1)
			->setDefaultValue(0);

		$form->addSubmit("confirm", MAIN_PAGE_SEARCH)
			->setAttribute("class","btn btn-primary btn-sm")
			->setAttribute("tabindex", $i+1);

		return $form;
	}
}