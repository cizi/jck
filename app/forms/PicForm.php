<?php

namespace App\Forms;

use App\Enum\WebWidthEnum;
use App\Model\BlockRepository;
use App\Model\LangRepository;
use App\Model\PicRepository;
use Nette;
use Nette\Application\UI\Form;

class PicForm {

    use Nette\SmartObject;

	/** @var FormFactory */
	private $factory;

	/** @var PicRepository */
	private $picRepository;

	/**
	 * GalleryForm constructor.
	 * @param FormFactory $factory
	 * @param PicRepository $picRepository
	 */
	public function __construct(FormFactory $factory, PicRepository $picRepository) {
		$this->factory = $factory;
		$this->picRepository = $picRepository;
	}

	/**
	 * @return Form
	 */
	public function create() {
		$form = $this->factory->create();
		$i = 1;
		$picsForSelect = [];
		$availablePics= $this->picRepository->load();
		foreach ($availablePics as $picEntity) {
			$picsForSelect[$picEntity->getId()] = Nette\Utils\Html::el('option', array('data-img-src' => $picEntity->getPath()));
		}
		$form->addMultiUpload("pics")
			->setAttribute("class", "form-control menuItem")
			->setAttribute("tabindex", $i+1);

		$form->addSubmit("confirm", PIC_SAVE_GALLERY)
			->setAttribute("class","btn btn-primary menuItem alignRight")
			->setAttribute("tabindex", $i+2);

		$form->addSubmit("deletePics", PIC_DELETE_PICS)
			->setAttribute("class","btn btn-danger menuItem alignRight")
			->setAttribute("tabindex", $i+2);

		$form->addMultiSelect("selectedPics", "", $picsForSelect)
			->setAttribute("class", "form-control menuItem")
			->setAttribute("tabindex", $i);

		return $form;
	}
}