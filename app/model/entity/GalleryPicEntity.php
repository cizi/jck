<?php

namespace App\Model\Entity;

class GalleryPicEntity {

	/** @var int */
	private $id;

	/** @var int */
	private $galleryId;

	/** @var int */
	private $sharedPicId;

	/**
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @param int $id
	 */
	public function setId($id) {
		$this->id = $id;
	}

	/**
	 * @return int
	 */
	public function getGalleryId() {
		return $this->galleryId;
	}

	/**
	 * @param int $galleryId
	 */
	public function setGalleryId($galleryId) {
		$this->galleryId = $galleryId;
	}

	/**
	 * @return int
	 */
	public function getSharedPicId() {
		return $this->sharedPicId;
	}

	/**
	 * @param int $sharedPicId
	 */
	public function setSharedPicId($sharedPicId) {
		$this->sharedPicId = $sharedPicId;
	}

	/**
	 * @return array
	 */
	public function extract() {
		return [
			'id' => $this->getId(),
			'gallery_id' => $this->getGalleryId(),
			'shared_pic_id' => $this->getSharedPicId()
		];
	}

	/**
	 * @param array $data
	 */
	public function hydrate(array $data) {
		$this->setId(isset($data['id']) ? $data['id'] : null);
		$this->setGalleryId(isset($data['gallery_id']) ? $data['gallery_id'] : null);
		$this->setSharedPicId(isset($data['shared_pic_id']) ? $data['shared_pic_id'] : null);
	}
}