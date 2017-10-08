<?php

namespace App\AdminModule\Presenters;

use App\Controller\FileController;
use App\Forms\PicForm;
use App\Model\Entity\PicEntity;
use Nette\Forms\Form;
use Nette\Http\FileUpload;

class PicPresenter extends SignPresenter {

	/** @var PicForm  */
	private $picForm;

	public function __construct(PicForm $picForm) {
		$this->picForm = $picForm;
	}

	public function actionDefault($id, $values = null) {
		if ($values != null) {
			$valuesArray['selectedPics'] = explode(EnumerationPresenter::NAME_SEPARATOR, $values);
			$this['picForm']->setDefaults($valuesArray);
		}
	}

	public function createComponentPicForm() {
		$form = $this->picForm->create();
		$form->onSuccess[] = $this->picFormSubmit;

		return $form;
	}

	/**
	 * @param Form $form
	 * @param $values
	 */
	public function picFormSubmit(Form $form, $values) {
		$deletation = (isset($form->getHttpData()['deletePics']));
		$supportedFileFormats = ["jpg", "png", "gif", "jpeg"];
		try {
			foreach ($values as $key => $value) {
				if (is_array($value) && ($key == 'selectedPics') && $deletation) {    // obrázky ke smazání nahrání
					$fileIds = [];
					foreach ($value as $fileId) {
						if ($this->picRepository->delete($fileId) == false) {
							$fileIds[] = $fileId;
						}
					}
					if (count($fileIds)) {
						throw new \Exception(implode(EnumerationPresenter::NAME_SEPARATOR, $fileIds));
					}
				}

				if (is_array($value) && ($key == 'pics')) {    // obrázky k nahrání
					/** @var FileUpload $file */
					foreach ($value as $file) {
						if ($file->name != "") {
							$fileController = new FileController();
							if ($fileController->upload($file, $supportedFileFormats,
									$this->getHttpRequest()->getUrl()->getBaseUrl()) == false
							) {
								throw new \Exception("");
							}
							$blockPic = new PicEntity();
							$blockPic->setPath($fileController->getPathDb());
							$this->picRepository->save($blockPic);
						}
					}
				}
			}
		} catch (\Exception $ex) {
			$values = $ex->getMessage();
			$this->flashMessage(PIC_DELETED_EXISTS, "alert-danger");
			$this->redirect("default", null, $values);
		}
		$this->redirect("default");
	}
}