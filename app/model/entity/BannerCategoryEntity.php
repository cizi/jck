<?php

namespace App\Model\Entity;

class BannerCategoryEntity {

	/** @var int */
	private $id;

	/** @var int */
	private $banner_id;

	/** @var int */
	private $menu_order;

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
	public function getBannerId() {
		return $this->banner_id;
	}

	/**
	 * @param int $banner_id
	 */
	public function setBannerId($banner_id) {
		$this->banner_id = $banner_id;
	}

	/**
	 * @return int
	 */
	public function getMenuOrder() {
		return $this->menu_order;
	}

	/**
	 * @param int $menu_order
	 */
	public function setMenuOrder($menu_order) {
		$this->menu_order = $menu_order;
	}

	/**
	 * @return array
	 */
	public function extract() {
		return [
			'id' => $this->getId(),
			'banner_id' => $this->getBannerId(),
			'menu_order' => $this->getMenuOrder()
		];
	}

	/**
	 * @param array $data
	 */
	public function hydrate(array $data) {
		$this->setId(isset($data['id']) ? $data['id'] : null);
		$this->setBannerId(isset($data['banner_id']) ? $data['banner_id'] : null);
		$this->setMenuOrder(isset($data['menu_order']) ? $data['menu_order'] : null);
	}
}