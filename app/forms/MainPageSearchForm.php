<?php

namespace App\Forms;

use App\Model\EnumerationRepository;
use Nette;
use Nette\Application\UI\Form;

class MainPageSearchForm {

    use Nette\SmartObject;

	/** @const formát data hledání */
	const SEARCH_DATE_FORMAT = 'd.m.Y';

	/** @var FormFactory */
	private $factory;

	/** @var  EnumerationRepository  */
	private $enumerationRepository;

	/**
	 * MainPageSearchForm constructor.
	 * @param FormFactory $factory
	 */
	public function __construct(
		FormFactory $factory,
		EnumerationRepository $enumerationRepository
	) {
		$this->factory = $factory;
		$this->enumerationRepository = $enumerationRepository;
	}

	/**
	 * @param Nette\Application\UI\Presenter $presenter
	 * @param string $lang
	 * @return Form
	 */
	public function create(Nette\Application\UI\Presenter $presenter, $lang) {
		$form = $this->factory->create();
		$form->getElementPrototype()->addAttributes(["onsubmit" => "return requiredFields();"]);

		$i = 0;
		$form->addText("from", MAIN_SEARCH_FROM)
			->setAttribute("readonly", "readonly")
			->setAttribute("validation", MAIN_SEARCH_FROM_REQ)
			->setAttribute("class", "form-control tinym_required_field")
			->setAttribute("tabindex", $i+1);

		$form->addText("to", MAIN_SEARCH_TO)
			->setAttribute("readonly", "readonly")
			->setAttribute("class", "form-control")
			->setAttribute("tabindex", $i+1);

		$form->addText("search", MAIN_SEARCH_SEARCH)
			->setAttribute("placeholder", MAIN_SEARCH_SEARCH)
			->setAttribute("class", "form-control")
			->setAttribute("tabindex", $i+1);

		$sublocations = $this->enumerationRepository->findEnumItemsForSelectWithEmpty($lang, EnumerationRepository::SUBLOKACE);
		$form->addSelect("sublocation", ARTICLE_SUBLOCATION, $sublocations)
			->setAttribute("class", "form-control")
			->setAttribute("tabindex", $i+1);

		$d = new \DateTime('now');
		$link = new Nette\Application\UI\Link($presenter, ":Frontend:Show:SearchDate", ['lang' => $lang, 'from' => $d->format(self::SEARCH_DATE_FORMAT)]);
		$form->addButton("today", MAIN_SEARCH_TODAY)
			->setAttribute("onclick", "location.href='" . $link . "'")
			->setAttribute("class","btn btn-info searchButton")
			->setAttribute("tabindex", $i+1);

		$d = new \DateTime('tomorrow');
		$link = new Nette\Application\UI\Link($presenter, ":Frontend:Show:SearchDate", ['lang' => $lang, 'from' => $d->format(self::SEARCH_DATE_FORMAT)]);
		$form->addButton("tomorrow", MAIN_SEARCH_TOMORROW)
			->setAttribute("onclick", "location.href='" . $link . "'")
			->setAttribute("class","btn btn-info searchButton")
			->setAttribute("tabindex", $i+1);


		$now = new \DateTime("now");
		$now->setTime(0,0);
		if (($now->format("l") == "Saturday") || ($now->format("l") == "Sunday")) {
			$dSaturday = clone ($now->format("l") == "Saturday" ? $now : $now->modify('-1 day'));
			$dSunday = clone ($now->format("l") == "Sunday" ? $now : $now->modify('+1 day'));
		} else {
			$dSaturday = new \DateTime("next saturday");
			$dSunday = new \DateTime("next sunday");
		}

		$searchParams = [
			'lang' => $lang,
			'from' => $dSaturday->format(self::SEARCH_DATE_FORMAT),
			'to'   => $dSunday->format(self::SEARCH_DATE_FORMAT)
		];

		$link = new Nette\Application\UI\Link($presenter, ":Frontend:Show:SearchDate", $searchParams);
		$form->addButton("weekend", MAIN_SEARCH_WEEKEND)
			->setAttribute("onclick", "location.href='" . $link . "'")
			->setAttribute("class","btn btn-info searchButton")
			->setAttribute("tabindex", $i+1);


		$form->addSubmit("searchSubmit", MAIN_SEARCH_SEARCH)
			->setAttribute("class","btn btn-success searchButton submitSearchButton")
			->setAttribute("tabindex", $i+1);

		return $form;
	}
}