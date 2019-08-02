<?php

namespace App\Forms;

use App\Enum\WebWidthEnum;
use App\Model\BlockRepository;
use App\Model\LangRepository;
use App\Model\PicRepository;
use Nette;
use Nette\Application\UI\Form;

class GalleryForm {

    use Nette\SmartObject;

	/** @var FormFactory */
	private $factory;

	/** @var LangRepository */
	private $langRepository;

	/** @var PicRepository */
	private $picRepository;

	/**
	 * GalleryForm constructor.
	 * @param FormFactory $factory
	 * @param LangRepository $langRepository
	 * @param PicRepository $picRepository
	 */
	public function __construct(FormFactory $factory, LangRepository $langRepository, PicRepository $picRepository) {
		$this->factory = $factory;
		$this->langRepository = $langRepository;
		$this->picRepository = $picRepository;
	}

	/**
	 * @return Form
	 */
	public function create() {
		$form = $this->factory->create();
		$form->getElementPrototype()->addAttributes(["onsubmit" => "return requiredFields();"]);

		$languages = $this->langRepository->findLanguages();
		$i = 1;
		foreach ($languages as $lang) {
			$container = $form->addContainer($lang);

			$container->addText("lang")
				->setAttribute("class", "form-control menuItem langDivider")
				->setAttribute("tabindex", "-1")
				->setAttribute("readonly", "readonly")
				->setValue($lang);

			$container->addText("header", GALLERY_NAME)
				->setAttribute("class", "form-control menuItem tinym_required_field")
				->setAttribute("placeholder", GALLERY_NAME)
				->setAttribute("validation", GALLERY_NAME_REQ)
				->setAttribute("tabindex", $i);

			$container->addText("desc", GALLERY_DESC)
				->setAttribute("class", "form-control menuItem")
				->setAttribute("placeholder", GALLERY_DESC)
				//->setAttribute("validation", GALLERY_DESC_REQ)
				->setAttribute("tabindex", $i);

			$container->addHidden('id');

			$i++;
		}

		$form->addCheckbox('on_main_page', " " . GALLERY_ON_MAIN_PAGE)
			->setAttribute("data-toggle", "toggle")
			->setAttribute("data-height", "25")
			->setAttribute("data-width", "50")
			->setDefaultValue("checked")
			->setAttribute("tabindex", $i+1);

		$form->addCheckbox('active', " " . GALLERY_ACTIVE)
			->setAttribute("data-toggle", "toggle")
			->setAttribute("data-height", "25")
			->setAttribute("data-width", "50")
			->setDefaultValue("checked")
			->setAttribute("tabindex", $i+1);

		$picsForSelect = [];
		$availablePics= $this->picRepository->load();
		foreach ($availablePics as $picEntity) {
			$picsForSelect[$picEntity->getId()] = Nette\Utils\Html::el('option', array('data-img-src' => $picEntity->getPath()));
		}
		$form->addMultiSelect("selectedPics", "", $picsForSelect)
			->setAttribute("class", "form-control menuItem")
			->setAttribute("tabindex", $i);

		$form->addHidden("id");
		$form->addHidden("inserted_timestamp");
		$form->addHidden("user_id");

		$form->addSubmit("confirm", GALLERY_SAVE_GALLERY)
			->setAttribute("class","btn btn-primary menuItem alignRight")
			->setAttribute("tabindex", $i+2);

		return $form;
	}
}