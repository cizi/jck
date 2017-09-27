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
	private $place;

	/** @var string */
	private $placeText;

	/** @var string */
	private $address;

	/** @var int */
	private $location;

	/** @var int */
	private $sublocation;

	/** @var string */
	private $contact;

	/** @var string  */
	private $ytUrl;

	/** @var int */
	private $showCounter;

	/** @var int */
	private $clickCounter;

	/** @var ArticleContentEntity[]  */
	private $contents;

	/** @var ArticleTimetableEntity[] */
	private $timetables;

	/** @var  */
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
	public function getPlace() {
		return $this->place;
	}

	/**
	 * @param int $place
	 */
	public function setPlace($place) {
		$this->place = $place;
	}

	/**
	 * @return string
	 */
	public function getPlaceText() {
		return $this->placeText;
	}

	/**
	 * @param string $placeText
	 */
	public function setPlaceText($placeText) {
		$this->placeText = $placeText;
	}

	/**
	 * @return string
	 */
	public function getAddress() {
		return $this->address;
	}

	/**
	 * @param string $address
	 */
	public function setAddress($address) {
		$this->address = $address;
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
	public function getContact() {
		return $this->contact;
	}

	/**
	 * @param string $contact
	 */
	public function setContact($contact) {
		$this->contact = $contact;
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
	 * @return ArticleTimetableEntity[]
	 */
	public function getTimetables() {
		return $this->timetables;
	}

	/**
	 * @param ArticleTimetableEntity[] $timetables
	 */
	public function setTimetables($timetables) {
		$this->timetables = $timetables;
	}

	/**
	 * @return mixed
	 */
	public function getCategories() {
		return $this->categories;
	}

	/**
	 * @param mixed $categories
	 */
	public function setCategories($categories) {
		$this->categories = $categories;
	}

	/**
	 * @return \DateTime
	 */
	public function getLastTimetableDate() {
		$lastDate = new \DateTime();
		foreach ($this->getTimetables() as $timetable) {
			if ($lastDate < $timetable->getDateTo()) {
				$lastDate = $timetable->getDateTo();
			}
		}

		return $lastDate;
	}

	/**
	 * @return array
	 */
	public function extract() {
		return [
			'id' => $this->getId(),
			'type' => $this->getType(),
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
			'place' => $this->getPlace(),
			'place_text' => $this->getPlaceText(),
			'address' => $this->getAddress(),
			'sublocation' => $this->getSublocation(),
			'yt_url' => $this->getYtUrl(),
			'show_counter' => $this->getShowCounter(),
			'click_counter' => $this->getClickCounter(),
			'pic_url' => $this->getPicUrl(),
			'contact' => $this->getContact()
		];
	}

	/**
	 * @param array $data
	 */
	public function hydrate(array $data) {
		$this->setId(isset($data['id']) ? $data['id'] : null);
		$this->setType(isset($data['type']) ? $data['type'] : null);
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
		$this->setPlace(isset($data['place']) ? $data['place'] : null);
		$this->setPlaceText(isset($data['place_text']) ? $data['place_text'] : null);
		$this->setAddress(isset($data['address']) ? $data['address'] : null);
		$this->setLocation(isset($data['location']) ? $data['location'] : null);
		$this->setSublocation(isset($data['sublocation']) ? $data['sublocation'] : null);
		$this->setYtUrl(isset($data['yt_url']) ? $data['yt_url'] : null);
		$this->setShowCounter(isset($data['show_counter']) ? $data['show_counter'] : null);
		$this->setClickCounter(isset($data['click_counter']) ? $data['click_counter'] : null);
		$this->setPicUrl(isset($data['pic_url']) ? $data['pic_url'] : null);
		$this->setContact(isset($data['contact']) ? $data['contact'] : null);
	}
}