<?php

namespace App\AdminModule\Presenters;

use App\Controller\FileController;
use App\Forms\GalleryFilterForm;
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

	/** @persistent */
	public $filter;

	/** @var GalleryForm  */
	private $galleryForm;

	/** @var GalleryFilterForm */
	private $galleryFilterForm;

	/**
	 * GalleryRepository constructor.
	 * @param GalleryFilterForm $galleryFilterForm
	 */
	public function __construct(GalleryForm $galleryForm, GalleryFilterForm $galleryFilterForm) {
		$this->galleryForm = $galleryForm;
		$this->galleryFilterForm = $galleryFilterForm;
	}

	public function actionDefault() {
		$filter = $this->decodeFilterFromQuery();
		$this['galleryFilterForm']->setDefaults($filter);
		$fulltext = (isset($filter['fulltext']) ? $filter['fulltext'] : null);

		$currentLang = $this->langRepository->getCurrentLang($this->session);
		$this->template->galleries = $this->galleryRepository->findGalleriesInLangByQuery($currentLang, $fulltext);
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
		$renderer->wrappers['control']['container'] = 'div class=col-md-10';
		$renderer->wrappers['label']['container'] = 'div class="col-md-2 control-label"';
		$renderer->wrappers['control']['description'] = 'span class=help-block';
		$renderer->wrappers['control']['errorcontainer'] = 'span class=help-block';
		$form->getElementPrototype()->class('form-horizontal');

		return $form;
	}

	public function createComponentGalleryFilterForm() {
		$form = $this->galleryFilterForm->create();
		$form->onSuccess[] = $this->galleryFilterFormSubmit;

		$renderer = $form->getRenderer();
		$renderer->wrappers['controls']['container'] = NULL;
		$renderer->wrappers['pair']['container'] = 'div class=form-group';
		$renderer->wrappers['pair']['.error'] = 'has-error';
		$renderer->wrappers['control']['container'] = 'div class="col-xs-6 col-sm-3 col-md-3 col-lg-4"';
		$renderer->wrappers['label']['container'] = 'div class="col-xs-6 col-sm-3 col-md-3 col-lg-2 control-label"';
		$renderer->wrappers['control']['description'] = 'span class=help-block';
		$renderer->wrappers['control']['errorcontainer'] = 'span class=help-block';

		return $form;
	}

	/**
	 * @param Form $form
	 */
	public function galleryFilterFormSubmit(Form $form) {
		$filter = "1&";
		$data = $form->getHttpData();

		foreach ($data as $key => $value) {
			if (is_array($value)) {	// kategorie
				$filter .= $key . "=";
				for ($i = 0; $i < count($value); $i++) {
					$filter .= $value[$i];
					$filter .= (($i+1) == count($value) ? "&" : ",");
				}
			} else if ($value != "") {
				$filter .= $key . "=" . $value . "&";
			}
		}
		$this->filter = $filter;
		$this->redirect("default");
	}

	/**
	 * @param Form $form
	 * @param $values
	 */
	public function galleryFormSubmit(Form $form, $values) {
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

	/**
	 * AJAX pro zobrazení / nezobrazení galerie na main page
	 */
	public function handleMainPageSwitch() {
		$data = $this->request->getParameters();
		$userId = $data['idGallery'];
		$switchTo = (!empty($data['to']) && $data['to'] == "false" ? false : true);

		if ($switchTo) {
			$this->galleryRepository->setGalleryOnMainPage($userId);
		} else {
			$this->galleryRepository->setGalleryNotMainPage($userId);
		}

		$this->terminate();
	}

}