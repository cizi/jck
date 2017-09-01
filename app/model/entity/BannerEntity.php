<?php

namespace App\Model\Entity;

use Dibi\DateTime;

class BannerEntity {

	/** @var int */
	private $id;

	/** @var string */
	private $title;

	/** @var string */
	private $img;

	/** @var string */
	private $url;

	/** @var int */
	private $bannerType;

	/** @var DateTime */
	private $dateStart;

	/** @var DateTime */
	private $dateEnd;

	/** @var bool */
	private $showOnMainPage;

	/** @var int */
	private $showCounter;

	/** @var int */
	private $clickCounter;

	/** @var int */
	private $userId;

	/** @var int */
	private $article_id;

	/** @var BannerCategoryEntity[] */
	private $categories;

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
	 * @return string
	 */
	public function getTitle() {
		return $this->title;
	}

	/**
	 * @param string $title
	 */
	public function setTitle($title) {
		$this->title = $title;
	}

	/**
	 * @return string
	 */
	public function getImg() {
		return $this->img;
	}

	/**
	 * @param string $img
	 */
	public function setImg($img) {
		$this->img = $img;
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
	 * @return int
	 */
	public function getBannerType() {
		return $this->bannerType;
	}

	/**
	 * @param int $bannerType
	 */
	public function setBannerType($bannerType) {
		$this->bannerType = $bannerType;
	}

	/**
	 * @return DateTime
	 */
	public function getDateStart() {
		return $this->dateStart;
	}

	/**
	 * @param DateTime $dateStart
	 */
	public function setDateStart($dateStart) {
		$this->dateStart = $dateStart;
	}

	/**
	 * @return DateTime
	 */
	public function getDateEnd() {
		return $this->dateEnd;
	}

	/**
	 * @param DateTime $dateEnd
	 */
	public function setDateEnd($dateEnd) {
		$this->dateEnd = $dateEnd;
	}

	/**
	 * @return bool
	 */
	public function isShowOnMainPage() {
		return $this->showOnMainPage;
	}

	/**
	 * @param bool $showOnMainPage
	 */
	public function setShowOnMainPage($showOnMainPage) {
		$this->showOnMainPage = $showOnMainPage;
	}

	/**
	 * @return int
	 */
	public function getShowCounter() {
		return $this->showCounter;
	}

	/**
	 * @param int $showCounter
	 */
	public function setShowCounter($showCounter) {
		$this->showCounter = $showCounter;
	}

	/**
	 * @return int
	 */
	public function getClickCounter() {
		return $this->clickCounter;
	}

	/**
	 * @param int $clickCounter
	 */
	public function setClickCounter($clickCounter) {
		$this->clickCounter = $clickCounter;
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
	 * @return int
	 */
	public function getArticleId() {
		return $this->article_id;
	}

	/**
	 * @param int $article_id
	 */
	public function setArticleId($article_id) {
		$this->article_id = $article_id;
	}

	/**
	 * @return BannerCategoryEntity[]
	 */
	public function getCategories() {
		return $this->categories;
	}

	/**
	 * @param BannerCategoryEntity[] $categories
	 */
	public function setCategories($categories) {
		$this->categories = $categories;
	}

	/**
	 * @return array
	 */
	public function extract() {
		return [
			'id' => $this->getId(),
			'title' => $this->getTitle(),
			'img' => $this->getImg(),
			'url' => $this->getUrl(),
			'banner_type' => $this->getBannerType(),
			'date_start' => $this->getDateStart(),
			'date_end' => $this->getDateEnd(),
			'show_on_main_page' => $this->isShowOnMainPage(),
			'show_counter' => $this->getShowCounter(),
			'click_counter' => $this->getClickCounter(),
			'user_id' => $this->getUserId(),
			'article_id' => $this->getArticleId()
		];
	}

	/**
	 * @param array $data
	 */
	public function hydrate(array $data) {
		$this->setId(isset($data['id']) ? $data['id'] : null);
		$this->setTitle(isset($data['title']) ? $data['title'] : null);
		$this->setImg(isset($data['img']) ? $data['img'] : null);
		$this->setUrl(isset($data['url']) ? $data['url'] : null);
		$this->setBannerType(isset($data['banner_type']) ? $data['banner_type'] : null);
		$this->setDateStart(isset($data['date_start']) ? $data['date_start'] : null);
		$this->setDateEnd(isset($data['date_end']) ? $data['date_end'] : null);
		$this->setShowOnMainPage(isset($data['show_on_main_page']) ? $data['show_on_main_page'] : null);
		$this->setShowCounter(isset($data['show_counter']) ? $data['show_counter'] : null);
		$this->setClickCounter(isset($data['click_counter']) ? $data['click_counter'] : null);
		$this->setUserId(isset($data['user_id']) ? $data['user_id'] : null);
		$this->setArticleId(isset($data['article_id']) ? $data['article_id'] : null);
	}

}