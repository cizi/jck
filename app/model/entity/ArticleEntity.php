<?php

namespace App\Model\Entity;

use Dibi\DateTime;

class ArticleEntity {

	/** @var int */
	private $id;

	/** @var int */
	private $type;

	/** @var int */
	private $validity;

	/** @var int */
	private $menuOrder;

	/** @var bool  */
	private $active;

	/** @var string */
	private $backgroundColor;

	/** @var string  */
	private $color;

	/** @var string */
	private $width;

	/** @var int */
	private $insertedBy;

	/** @var DateTime  */
	private $insertedTimestamp;

	/** @var int */
	private $picId;

	/** @var string */
	private $picUrl;

	/** @var string */
	private $url;

	/** @var string */
	private $fbUrl;

	/** @var int */
	private $location;

	/** @var int */
	private $sublocation;

	/** @var string  */
	private $ytUrl;

	/** @var int */
	private $viewsCount;

	/** @var ArticleContentEntity[]  */
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
	 * @return int
	 */
	public function getType() {
		return $this->type;
	}

	/**
	 * @param int $type
	 */
	public function setType($type) {
		$this->type = $type;
	}

	/**
	 * @return int
	 */
	public function getMenuOrder() {
		return $this->menuOrder;
	}

	/**
	 * @param int $menuOrder
	 */
	public function setMenuOrder($menuOrder) {
		$this->menuOrder = $menuOrder;
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
	 * @return string
	 */
	public function getBackgroundColor() {
		return $this->backgroundColor;
	}

	/**
	 * @param string $backgroundColor
	 */
	public function setBackgroundColor($backgroundColor) {
		$this->backgroundColor = $backgroundColor;
	}

	/**
	 * @return string
	 */
	public function getColor() {
		return $this->color;
	}

	/**
	 * @param string $color
	 */
	public function setColor($color) {
		$this->color = $color;
	}

	/**
	 * @return string
	 */
	public function getWidth() {
		return $this->width;
	}

	/**
	 * @param string $width
	 */
	public function setWidth($width) {
		$this->width = $width;
	}

	/**
	 * @return int
	 */
	public function getInsertedBy() {
		return $this->insertedBy;
	}

	/**
	 * @param int $insertedBy
	 */
	public function setInsertedBy($insertedBy) {
		$this->insertedBy = $insertedBy;
	}

	/**
	 * @return DateTime
	 */
	public function getInsertedTimestamp() {
		return $this->insertedTimestamp;
	}

	/**
	 * @return int
	 */
	public function getPicId() {
		return $this->picId;
	}

	/**
	 * @param int $picId
	 */
	public function setPicId($picId) {
		$this->picId = $picId;
	}

	/**
	 * @param DateTime $insertedTimestamp
	 */
	public function setInsertedTimestamp($insertedTimestamp) {
		$this->insertedTimestamp = $insertedTimestamp;
	}

	/**
	 * @return ArticleContentEntity[]
	 */
	public function getContents() {
		return $this->contents;
	}

	/**
	 * @param ArticleContentEntity[] $contents
	 */
	public function setContents($contents) {
		$this->contents = $contents;
	}

	/**
	 * @return int
	 */
	public function getValidity() {
		return $this->validity;
	}

	/**
	 * @param int $validity
	 */
	public function setValidity($validity) {
		$this->validity = $validity;
	}

	/**
	 * @return string
	 */
	public function getUrl() {
		return $this->url;
	}

	/**
	 * @param string $url
	 */
	public function setUrl($url) {
		$this->url = $url;
	}

	/**
	 * @return string
	 */
	public function getFbUrl() {
		return $this->fbUrl;
	}

	/**
	 * @param string $fbUrl
	 */
	public function setFbUrl($fbUrl) {
		$this->fbUrl = $fbUrl;
	}

	/**
	 * @return int
	 */
	public function getLocation() {
		return $this->location;
	}

	/**
	 * @param int $location
	 */
	public function setLocation($location) {
		$this->location = $location;
	}

	/**
	 * @return int
	 */
	public function getSublocation() {
		return $this->sublocation;
	}

	/**
	 * @param int $sublocation
	 */
	public function setSublocation($sublocation) {
		$this->sublocation = $sublocation;
	}

	/**
	 * @return string
	 */
	public function getYtUrl() {
		return $this->ytUrl;
	}

	/**
	 * @param string $ytUrl
	 */
	public function setYtUrl($ytUrl) {
		$this->ytUrl = $ytUrl;
	}

	/**
	 * @return int
	 */
	public function getViewsCount() {
		return $this->viewsCount;
	}

	/**
	 * @param int $viewsCount
	 */
	public function setViewsCount($viewsCount) {
		$this->viewsCount = $viewsCount;
	}

	/**
	 * @return string
	 */
	public function getPicUrl() {
		return $this->picUrl;
	}

	/**
	 * @param string $picUrl
	 */
	public function setPicUrl($picUrl) {
		$this->picUrl = $picUrl;
	}

	/**
	 * @return array
	 */
	public function extract() {
		return [
			'id' => $this->getId(),
			'type' => $this->getType(),
			'menu_order' => $this->getMenuOrder(),
			'active' => ($this->isActive() ? 1 : 0),
			'background_color' => $this->getBackgroundColor(),
			'color' => $this->getColor(),
			'width' => $this->getWidth(),
			'inserted_by' => $this->getInsertedBy(),
			'inserted_timestamp' => $this->getInsertedTimestamp(),
			'pic_id' => $this->getPicId(),
			'validity' => $this->getValidity(),
			'url' => $this->getUrl(),
			'fb_url' => $this->getFbUrl(),
			'location' => $this->getLocation(),
			'sublocation' => $this->getSublocation(),
			'yt_url' => $this->getYtUrl(),
			'views_count' => $this->getViewsCount(),
			'pic_url' => $this->getPicUrl()
		];
	}

	/**
	 * @param array $data
	 */
	public function hydrate(array $data) {
		$this->setId(isset($data['id']) ? $data['id'] : null);
		$this->setType(isset($data['type']) ? $data['type'] : null);
		$this->setMenuOrder(isset($data['menu_order']) ? $data['menu_order'] : null);
		$this->setActive(isset($data['active']) ? $data['active'] : null);
		$this->setBackgroundColor(isset($data['background_color']) ? $data['background_color'] : null);
		$this->setColor(isset($data['color']) ? $data['color'] : null);
		$this->setWidth(isset($data['width']) ? $data['width'] : null);
		$this->setInsertedBy(isset($data['inserted_by']) ? $data['inserted_by'] : null);
		$this->setInsertedTimestamp(isset($data['inserted_timestamp']) ? $data['inserted_timestamp'] : null);
		$this->setPicId(isset($data['pic_id']) ? $data['pic_id'] : null);
		$this->setValidity(isset($data['validity']) ? $data['validity'] : null);
		$this->setUrl(isset($data['url']) ? $data['url'] : null);
		$this->setFbUrl(isset($data['fb_url']) ? $data['fb_url'] : null);
		$this->setLocation(isset($data['location']) ? $data['location'] : null);
		$this->setSublocation(isset($data['sublocation']) ? $data['sublocation'] : null);
		$this->setYtUrl(isset($data['yt_url']) ? $data['yt_url'] : null);
		$this->setViewsCount(isset($data['views_count']) ? $data['views_count'] : null);
		$this->setPicUrl(isset($data['pic_url']) ? $data['pic_url'] : null);
	}
}