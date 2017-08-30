<?php

namespace App\AdminModule\Presenters;

use App\Controller\FileController;
use App\Controller\MenuController;
use App\Forms\BannerForm;
use App\Model\BannerRepository;
use App\Model\Entity\BannerCategoryEntity;
use App\Model\Entity\BannerEntity;
use App\Model\Entity\MenuEntity;
use App\Model\EnumerationRepository;
use App\Model\LangRepository;
use Nette\Forms\Form;
use Nette\Http\FileUpload;
use Nette\Utils\ArrayHash;

class BannerPresenter extends SignPresenter {

	/** @var BannerRepository */
	private $bannerRepository;

	/** @var EnumerationRepository */
	private $enumerationRepository;

	/** @var LangRepository */
	private $langRepository;

	/** @var BannerForm */
	private $bannerForm;

	/** @var MenuController */
	private $menuController;

	public function __construct(
		BannerRepository $bannerRepository,
		EnumerationRepository $enumerationRepository,
		LangRepository $langRepository,
		BannerForm $bannerForm,
		MenuController $menuController
	) {
		$this->bannerRepository = $bannerRepository;
		$this->enumerationRepository = $enumerationRepository;
		$this->langRepository = $langRepository;
		$this->bannerForm = $bannerForm;
		$this->menuController = $menuController;
	}

	/**
	 * @param int $id
	 */
	public function actionDefault($id) {
		$currentLang = $this->langRepository->getCurrentLang($this->session);
		$this->template->banners = $this->bannerRepository->findBanners();
		$this->template->enumRepo = $this->enumerationRepository;
		$this->template->currentLang = $currentLang;
		$this->template->menuCategories = $this->menuController->findMenuCategoriesForSelect($currentLang);
	}

	/**
	 * @param int $id
	 * @param array|null $values
	 */
	public function actionEdit($id, array $values = null) {
		if ($values != null) {
			$this['bannerForm']->setDefaults($values);
		}
		if (!empty($id)) {
			$categoryEntity = $this->bannerRepository->getBanner($id);
			$values = $categoryEntity->extract();
			if ($categoryEntity->getDateStart() != null) {
				$values['date_start'] = $categoryEntity->getDateStart()->format('Y-m-d');
			}
			if ($categoryEntity->getDateEnd() != null) {
				$values['date_end'] = $categoryEntity->getDateEnd()->format('Y-m-d');
			}
			foreach ($categoryEntity->getCategories() as $bannerCategoryEntity) {
				$values['bannerCategories'][] = $bannerCategoryEntity->getMenuOrder();
			}
			$this['bannerForm']->setDefaults($values);
		}
		$this->template->currentLang= $this->langRepository->getCurrentLang($this->session);
	}

	public function createComponentBannerForm() {
		$form = $this->bannerForm->create($this->langRepository->getCurrentLang($this->session));
		$form->onSuccess[] = $this->bannerFormSubmit;

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
	 * @param ArrayHash $values
	 */
	public function bannerFormSubmit(Form $form, $values) {
		$bannerEntity = new BannerEntity();
		$bannerEntity->hydrate((array)$values);

		$error = false;
		$supportedFileFormats = ["jpg", "png", "gif"];
		$categories = [];
		foreach($values as $key => $value) {
			/* if ($value instanceof ArrayHash) {	// language mutation
				$articleContentEntity = new ArticleContentEntity();
				$articleContentEntity->hydrate((array)$value);
				$articleContentEntity->setLang($key);

				$mutation[] = $articleContentEntity;
			} */
			if ($key == "imgSelector") {	// obrázky
				/** @var FileUpload $file */
				$file = $value;
				if ($file->name != "") {
					$fileController = new FileController();
					if ($fileController->upload($file, $supportedFileFormats, $this->getHttpRequest()->getUrl()->getBaseUrl()) == false) {
						$error = true;
						break;
					}
					$bannerEntity->setImg($fileController->getPathDb());
				}
			}
			if ($key == "bannerCategories") {	// kategorie
				foreach ($value as $categoryId) {
					$bannerCategory = new BannerCategoryEntity();
					$bannerCategory->setMenuOrder($categoryId);
					$categories[] = $bannerCategory;
				}
			}
		}

		$bannerEntity->setCategories($categories);
		if ($error) {
			$flashMessage = sprintf(UNSUPPORTED_UPLOAD_FORMAT, implode(",", $supportedFileFormats));
			$this->flashMessage($flashMessage, "alert-danger");
			$this->redirect("edit", null, $values);
		} else {
			if ($this->bannerRepository->saveCompleteBanner($bannerEntity, $this->getUser()->getId()) == false) {
				$this->flashMessage(ARTICLE_SAVE_FAILED, "alert-danger");
				$this->redirect("edit", null, $values);
			}
		}
		$this->redirect("default");
	}

	/**
	 * AJAX pro aktivace / deaktivace uživatele
	 */
	public function handleActiveSwitch() {
		$data = $this->request->getParameters();
		$bannerId = $data['idBanner'];
		$switchTo = (!empty($data['to']) && $data['to'] == "false" ? false : true);

		if ($switchTo) {
			$this->bannerRepository->setBannerActiveOnMainPage($bannerId);
		} else {
			$this->bannerRepository->setBannerInactiveOnMainPage($bannerId);
		}

		$this->terminate();
	}

	/**
	 * @param int $id
	 */
	public function actionDelete($id) {
		if ($this->bannerRepository->delete($id)) {
			$this->flashMessage(BANNER_DELETE_OK, "alert-success");
		} else {
			$this->flashMessage(BANNER_DELETE_FAILED, "alert-danger");
		}
		$this->redirect("default");
	}
}