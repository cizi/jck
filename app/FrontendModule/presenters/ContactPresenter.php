<?php

namespace App\FrontendModule\Presenters;

use App\Controller\FileController;
use App\Model\WebconfigRepository;
use Nette\Forms\Form;
use Nette\Http\FileUpload;

class ContactPresenter extends BasePresenter {

	public function actionDefault($lang, $id, $seoText) {

	}

	/**
	 * Proceed contact form
	 *
	 * @param Form $form
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