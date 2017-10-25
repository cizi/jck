<?php

namespace App\FrontendModule\Presenters;


use App\Forms\FulltextSearchForm;
use App\Model\ArticleRepository;
use App\Model\EnumerationRepository;
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
	public function actionDefault($lang, $id, $seoText, $startDate, $sublocation = null, $direct = null) {
		$this->checkLanguage($lang);
		if ($startDate == null) {
			$dateFrom = new DateTime();
		} else {
			$dateFrom = DateTime::createFromFormat("Y-m-d", $startDate);
		}
		$endDate = clone $dateFrom;
		$this->template->startDate = $dateFrom;
		$this->template->endDate = $endDate->modify( '+7 day' );
		if ($direct == null) {
			$articles = $this->articleRepository->findActiveArticlesInLangByDate($lang, $dateFrom, null, $endDate, EnumerationRepository::TYP_PRISPEVKU_AKCE_ORDER, $sublocation);
		} else {
			$this->template->directText = sprintf(ARTICLE_CALENDAR_DIRECT, $dateFrom->format(ArticleRepository::URL_DATE_MASK));
			$articles = $this->articleRepository->findActiveArticlesInLangByDate($lang, $dateFrom, null, $dateFrom, EnumerationRepository::TYP_PRISPEVKU_AKCE_ORDER, $sublocation);
		}
		$this->template->articles = $articles;
		$this->template->sublocation = $sublocation;

		if ($sublocation != null) {
			$this["destinationForm"]["destination"]->setDefaultValue($sublocation);
		}
		$this["destinationForm"]["startDate"]->setDefaultValue($dateFrom->format('Y-m-d'));
	}

	public function actionPlusWeek($lang, $id, $seoText, $sublocation) {
		$this->checkLanguage($lang);
		$startDate = DateTime::createFromFormat("Y-m-d", $id);
		$plusWeek = $startDate->modify('+7 days');
		$this->redirect("default", $lang, $id, $seoText, $plusWeek->format('Y-m-d'), $sublocation);
	}

	public function actionMinusWeek($lang, $id, $seoText, $sublocation) {
		$this->checkLanguage($lang);
		$startDate = DateTime::createFromFormat("Y-m-d", $id);
		$minusWeek = $startDate->modify('-7 days');
		$this->redirect("default", $lang, $id, $seoText, $minusWeek->format('Y-m-d'), $sublocation);
	}

	/**
	 * @return Form
	 */
	protected function createComponentDestinationForm() {
		$form = $this->searchForm->create($this->langRepository->getCurrentLang($this->session));
		$form->addHidden("startDate" );
		$form['destination']->setAttribute("onchange", "this.form.submit();");

		$form->onSuccess[] = $this->destinationCalendar;

		return $form;
	}

	/**
	 * Načte data z formuláře a přesměruje (s daty formuláře) na default
	 * @param Form $form
	 * @param $values
	 */
	public function destinationCalendar(Form $form, $values) {
		$lang = $this->langRepository->getCurrentLang($this->session);
		$destination = ((isset($values['destination']) && ($values['destination'] != 0)) ? $values['destination'] : null);
		$startDate = $values['startDate'];
		$this->redirect("default", $lang, null, null, $startDate, $destination);
	}
}