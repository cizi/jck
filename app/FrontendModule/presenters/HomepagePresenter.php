<?php

namespace App\FrontendModule\Presenters;

use App\Controller\FileController;
use App\Model\Entity\BlockContentEntity;
use App\Model\Entity\MenuEntity;
use App\Model\EnumerationRepository;
use App\Model\PicRepository;
use Nette;
use App\Enum\WebWidthEnum;
use App\Model\WebconfigRepository;
use Nette\Http\FileUpload;

class HomepagePresenter extends BasePresenter {

	const MAX_TEXT_ARTICLES = 4;
	const MAX_GALLERIES = 4;

	/**
	 * @param string $lang
	 * @param string $id
	 */
	public function renderDefault($lang, $id) {
		if (empty($lang)) {
			$lang = $this->langRepository->getCurrentLang($this->session);
			$this->redirect("default", [ 'lang' => $lang, 'id' => $id]);
		}

		// what if link will have the same shortcut like language
		if (isset($availableLangs[$lang]) && ($lang != $this->langRepository->getCurrentLang($this->session))) {
			$this->langRepository->switchToLanguage($this->session, $lang);
			$this->redirect("default", [ 'lang' => $lang, 'id' => $id ]);
		} else {
			if ((empty($id) || ($id == "")) && !empty($lang) && (!isset($availableLangs[$lang]))) {
				$id = $lang;
			}
			if (empty($id) || ($id == "")) {    // try to find default
				$userBlocks[] = $this->getDefaultBlock();
			} else {
				$userBlocks = $this->blockRepository->findAddedBlockFronted($id,
					$this->langRepository->getCurrentLang($this->session));
				if (empty($userBlocks)) {
					$userBlocks[] = $this->getDefaultBlock();
				}
			}
			// because of sitemap.xml
			$allWebLinks = $this->menuRepository->findAllItems();
			$this->template->availableLinks = $allWebLinks;
			/** @var MenuEntity $menuLink */
			foreach($allWebLinks as $menuLink) {
				if ($menuLink->getLink() == $id) {
					$this->template->currentLink = $menuLink;
				}
}			}

			$this->template->widthEnum = new WebWidthEnum();
			$this->template->textArticles = $this->articleRepository->findActiveArticlesInLang($lang, EnumerationRepository::TYP_PRISPEVKU_CLANEK_ORDER);
			$this->template->maxTextArticles = self::MAX_TEXT_ARTICLES;
			$this->template->maxGalleries = self::MAX_GALLERIES;
	}

	/**
	 * @return Nette\Application\UI\Form
	 */
	public function createComponentMainPageSearchForm() {
		$form = $this->mainPageSearchForm->create($this, $this->langRepository->getCurrentLang($this->session));
		$form->onSuccess = $this->mainPageSearchFormSubmit;

		return $form;
	}

	/**
	 * @param Nette\Forms\Form $form
	 * @param $values
	 */
	public function mainPageSearchFormSubmit(Nette\Forms\Form $form, $values) {

	}

	/**
	 * Proceed contact form
	 *
	 * @param Nette\Application\UI\Form $form
	 * @param $values
	 * @throws \Exception
	 * @throws \phpmailerException
	 */
	public function contactFormSubmitted($form, $values) {
		if (
			isset($values['contactEmail']) && $values['contactEmail'] != ""
			&& isset($values['name']) && $values['name'] != ""
			&& isset($values['subject']) && $values['subject'] != ""
			&& isset($values['text']) && $values['text'] != ""
		) {
			$supportedFilesFormat = ["png", "jpg", "bmp", "pdf", "doc", "xls", "docx", "xlsx"];
			$fileError = false;
			$path = "";
			if (!empty($values['attachment'])) {
				/** @var FileUpload $file */
				$file = $values['attachment'];
				if (!empty($file->name)) {
					$fileController = new FileController();
					if ($fileController->upload($file, $supportedFilesFormat, $this->getHttpRequest()->getUrl()->getBaseUrl()) == false) {
						$fileError = true;
						$this->flashMessage(CONTACT_FORM_UNSUPPORTED_FILE_FORMAT, "alert-danger");
					} else {
						$path = $fileController->getPath();
					}
				}
			}

			if ($fileError == false) {
				$email = new \PHPMailer();
				$email->CharSet = "UTF-8";
				$email->From = $values['contactEmail'];
				$email->FromName = $values['name'];
				$email->Subject = CONTACT_FORM_EMAIL_MY_SUBJECT . " - " . $values['subject'];
				$email->Body = $values['text'];
				$email->AddAddress($this->webconfigRepository->getByKey(WebconfigRepository::KEY_CONTACT_FORM_RECIPIENT, WebconfigRepository::KEY_LANG_FOR_COMMON));
				if (!empty($path)) {
					$email->AddAttachment($path);
				}
				$email->Send();
				$this->flashMessage(CONTACT_FORM_WAS_SENT, "alert-success");
			}
		} else {
			$this->flashMessage(CONTACT_FORM_SENT_FAILED, "alert-danger");
		}
		$this->redirect("default");
	}
}
