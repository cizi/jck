<?php

namespace App\FrontendModule\Presenters;


use App\Forms\FulltextSearchForm;
use Dibi\DateTime;
use Nette\Forms\Form;

class CalendarPresenter extends BasePresenter {

	/** @var FulltextSearchForm */
	private $fulltextSearchForm;

	public function __construct(FulltextSearchForm $fulltextSearchForm) {
		$this->fulltextSearchForm = $fulltextSearchForm;
	}

	/**
	 * @param $lang
	 * @param $id
	 * @param $seoText
	 */
	public function actionDefault($lang, $id, $seoText, $startDate) {
		if ($startDate == null) {
			$dateFrom = new DateTime();
		} else {
			$dateFrom = DateTime::createFromFormat("Y-m-d", $startDate);
		}
		$endDate = clone $dateFrom;
		$this->template->startDate = $dateFrom;
		$this->template->endDate = $endDate->modify( '+7 day' );

		$this->template->articles = $this->articleRepository->findActiveArticlesInLangByDate($lang, $dateFrom);
	}

	public function actionPlusWeek($lang, $id, $seoText) {
		$startDate = DateTime::createFromFormat("Y-m-d", $id);
		$plusWeek = $startDate->modify('+7 days');
		$this->redirect("default", $lang, $id, $seoText, $plusWeek->format('Y-m-d'));
	}

	public function actionMinusWeek($lang, $id, $seoText) {
		$startDate = DateTime::createFromFormat("Y-m-d", $id);
		$minusWeek = $startDate->modify('-7 days');
		$this->redirect("default", $lang, $id, $seoText, $minusWeek->format('Y-m-d'));
	}
}