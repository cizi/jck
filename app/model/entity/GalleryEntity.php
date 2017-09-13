<?php

namespace App\Model\Entity;

use Dibi\DateTime;

class GalleryEntity {

	/** @var  int */
	private $id;

	/** @var  bool */
	private $active;

	/** @var  DateTime */
	private $insertedTimestamp;

	/** @var  int */
	private $userId;

	/** @var GalleryPicEntity[] */
	private $pics;

	/** @var GalleryContentEntity[] */
	private $contents;

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
	 * @return bool
	 */
	public function isActive() {
		return $this->active;
	}

	/**
	 * @param bool $active
	 */
	public function setActive($active) {
		$this->active = $active;
	}

	/**
	 * @return DateTime
	 */
	public function getInsertedTimestamp() {
		return $this->insertedTimestamp;
	}

	/**
	 * @param DateTime $insertedTimestamp
	 */
	public function setInsertedTimestamp($insertedTimestamp) {
		$this->insertedTimestamp = $insertedTimestamp;
	}

	/**
	 * @return int
	 */
	public function getUserId() {
		return $this->userId;
	}

	/**
	 * @param int $userId
	 */
	public function setUserId($userId) {
		$this->userId = $userId;
	}

	/**
	 * @return GalleryPicEntity[]
	 */
	public function getPics() {
		return $this->pics;
	}

	/**
	 * @param GalleryPicEntity[] $pics
	 */
	public function setPics($pics) {
		$this->pics = $pics;
	}

	/**
	 * @return GalleryContentEntity[]
	 */
	public function getContents() {
		return $this->contents;
	}

	/**
	 * @param GalleryContentEntity[] $contents
	 */
	public function setContents($contents) {
		$this->contents = $contents;
	}

	/**
	 * @return array
	 */
	public function extract() {
		return [
			'id' => $this->getId(),
			'active' => $this->isActive(),
			'inserted_timestamp' => $this->getInsertedTimestamp(),
			'user_id' => $this->getUserId(),
		];
	}

	/**
	 * @param array $data
	 */
	public function hydrate($data) {
		$this->setId(isset($data['id']) ? $data['id'] : null);
		$this->setActive(isset($data['active']) ? $data['active'] : null);
		$this->setInsertedTimestamp(isset($data['inserted_timestamp']) ? $data['inserted_timestamp'] : null);
		$this->setUserId(isset($data['user_id']) ? $data['user_id'] : null);
	}
}