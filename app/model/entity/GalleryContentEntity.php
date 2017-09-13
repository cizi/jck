<?php

namespace App\Model\Entity;

class GalleryContentEntity {

	/** @var int */
	private $id;

	/** @var int */
	private $galleryId;

	/** @var string */
	private $header;

	/** @var string  */
	private $desc;

	/** @var string */
	private $lang;

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
	 * @param int $gallery_id
	 */
	public function setGalleryId($galleryId) {
		$this->galleryId = $galleryId;
	}

	/**
	 * @return string
	 */
	public function getHeader() {
		return $this->header;
	}

	/**
	 * @param string $header
	 */
	public function setHeader($header) {
		$this->header = $header;
	}

	/**
	 * @return string
	 */
	public function getDesc() {
		return $this->desc;
	}

	/**
	 * @param string $desc
	 */
	public function setDesc($desc) {
		$this->desc = $desc;
	}

	/**
	 * @return string
	 */
	public function getLang() {
		return $this->lang;
	}

	/**
	 * @param string $lang
	 */
	public function setLang($lang) {
		$this->lang = $lang;
	}

	/**
	 * @return array
	 */
	public function extract() {
		return [
			'id' => $this->getId(),
			'gallery_id' => $this->getGalleryId(),
			'header' => $this->getHeader(),
			'desc' => $this->getDesc(),
			'lang' => $this->getLang()
		];
	}

	/**
	 * @param array $data
	 */
	public function hydrate($data) {
		$this->setId(isset($data['id']) ? $data['id'] : null);
		$this->setGalleryId(isset($data['gallery_id']) ? $data['gallery_id'] : null);
		$this->setHeader(isset($data['header']) ? $data['header'] : null);
		$this->setDesc(isset($data['desc']) ? $data['desc'] : null);
		$this->setLang(isset($data['lang']) ? $data['lang'] : null);
	}
}