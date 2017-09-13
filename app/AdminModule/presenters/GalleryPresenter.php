<?php

namespace App\AdminModule\Presenters;

use App\Controller\FileController;
use App\Forms\GalleryForm;
use App\Model\Entity\GalleryContentEntity;
use App\Model\Entity\GalleryEntity;
use App\Model\Entity\GalleryPicEntity;
use App\Model\Entity\PicEntity;
use Dibi\DateTime;
use Nette\Forms\Form;
use Nette\Http\FileUpload;
use Nette\Utils\ArrayHash;

class GalleryPresenter extends SignPresenter {

	/** @var GalleryForm  */
	private $galleryForm;

	public function __construct(GalleryForm $galleryForm) {
		$this->galleryForm = $galleryForm;
	}

	public function actionDefault() {
		$currentLang = $this->langRepository->getCurrentLang($this->session);
		$this->template->galleries = $this->galleryRepository->findGalleriesInLang($currentLang);
	}

	/**
	 * @param int $id
	 */
	public function actionEdit($id, $seotext, $values = []) {
		if (!empty($values)) {
			$this['galleryForm']->setDefaults($values);
		}
		if (!empty($id)) {
			$galleryEntity = $this->galleryRepository->getGallery($id);
			$defaults = $galleryEntity->extract();
			foreach ($galleryEntity->getContents() as $galleryContentEntity) {
				$defaults[$galleryContentEntity->getLang()] = $galleryContentEntity->extract();
			}
			foreach ($galleryEntity->getPics() as $galleryPicEntity) {
				$defaults['selectedPics'][] = $galleryPicEntity->getSharedPicId();
			}
			$this['galleryForm']->setDefaults($defaults);
		}

		$this->template->galleryId = $id;
	}

	public function createComponentGalleryForm() {
		$form = $this->galleryForm->create();
		$form->onSuccess[] = $this->galleryFormSubmit;

		$renderer = $form->getRenderer();
		$renderer->wrappers['controls']['container'] = NULL;
		$renderer->wrappers['pair']['container'] = 'div class=form-group';
		$renderer->wrappers['pair']['.error'] = 'has-error';
		$renderer->wrappers['control']['container'] = 'div class=col-md-6';
		$renderer->wrappers['label']['container'] = 'div class="col-md-4 control-label"';
		$renderer->wrappers['control']['description'] = 'span class=help-block';
		$renderer->wrappers['control']['errorcontainer'] = 'span class=help-block';
		$form->getElementPrototype()->class('form-horizontal');

		return $form;
	}

	/**
	 * @param Form $form
	 * @param $values
	 */
	public function galleryFormSubmit(Form $form, $values) {
		$error = false;
		$supportedFileFormats = ["jpg", "png", "doc"];
		foreach($values as $key => $value) {
			if (is_array($value) && $key == "pics") {	// obrázky
				/** @var FileUpload $file */
				foreach ($value as $file) {
					if ($file->name != "") {
						$fileController = new FileController();
						if ($fileController->upload($file, $supportedFileFormats, $this->getHttpRequest()->getUrl()->getBaseUrl()) == false) {
							$error = true;
							break;
						}
						$blockPic = new PicEntity();
						$blockPic->setPath($fileController->getPathDb());
						$this->picRepository->save($blockPic);
					}
				}
			}
		}
		if ($error) {
			$flashMessage = sprintf(UNSUPPORTED_UPLOAD_FORMAT, implode(",", $supportedFileFormats));
			$this->flashMessage($flashMessage, "alert-danger");
			$this->redirect("edit", $values['id'], "", $values);
		}

		if (isset($form->getHttpData()['uploadPicture'])) {
			$this->redirect("edit", $values['id'], "", $values);
		} else {
			$galleryEntity = new GalleryEntity();
			$galleryEntity->hydrate((array)$values);
			if (empty($galleryEntity->getInsertedTimestamp())) {
				$galleryEntity->setInsertedTimestamp(new DateTime());
			}
			if (empty($galleryEntity->getUserId())) {
				$galleryEntity->setUserId($this->getUser()->getId());
			}
			$galleryContents = [];
			$galleryPics = [];
			foreach($values as $key => $value) {
				if (($value instanceof ArrayHash) && ($key != "selectedPics")) {	// language mutation
					$galleryContentEnt = new GalleryContentEntity();
					$galleryContentEnt->hydrate($value);
					$galleryContentEnt->setLang($key);
					$galleryContents[] = $galleryContentEnt;
				}
				if ($key == "selectedPics") {
					foreach ($value as $picId) {
						$galleryPicEnt = new GalleryPicEntity();
						$galleryPicEnt->setSharedPicId($picId);
						$galleryPics[] = $galleryPicEnt;
					}
				}
			}
			$galleryEntity->setContents($galleryContents);
			$galleryEntity->setPics($galleryPics);
			if ($this->galleryRepository->saveCompleteGallery($galleryEntity) == false) {
				$this->flashMessage(BLOCK_SETTINGS_ITEM_SAVED_FAILED, "alert-danger");
				$this->redirect("edit", null, "", $values);
			}
		}
		$this->redirect("default");
	}

	public function actionDelete($id) {
		$this->galleryRepository->delete($id);
		$this->redirect("default");
	}

	/**
	 * AJAX pro aktivace / deaktivace galerie
	 */
	public function handleActiveSwitch() {
		$data = $this->request->getParameters();
		$userId = $data['idGallery'];
		$switchTo = (!empty($data['to']) && $data['to'] == "false" ? false : true);

		if ($switchTo) {
			$this->galleryRepository->setGalleryActive($userId);
		} else {
			$this->galleryRepository->setGalleryInactive($userId);
		}

		$this->terminate();
	}

}