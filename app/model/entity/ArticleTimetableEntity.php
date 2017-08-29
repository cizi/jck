<?php

namespace App\Model\Entity;

use Dibi\DateTime;

class ArticleTimetableEntity {

	/** @var int */
	private $id;

	/** @var int */
	private $articleId;

	/** @var DateTime */
	private $dateFrom;

	/** @var DateTime */
	private $dateTo;

	/** @var DateTime */
	private $time;

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
	public function getArticleId() {
		return $this->articleId;
	}

	/**
	 * @param int $articleId
	 */
	public function setArticleId($articleId) {
		$this->articleId = $articleId;
	}

	/**
	 * @return DateTime
	 */
	public function getDateFrom() {
		return $this->dateFrom;
	}

	/**
	 * @param DateTime $dateFrom
	 */
	public function setDateFrom($dateFrom) {
		$this->dateFrom = $dateFrom;
	}

	/**
	 * @return DateTime
	 */
	public function getDateTo() {
		return $this->dateTo;
	}

	/**
	 * @param DateTime $dateTo
	 */
	public function setDateTo($dateTo) {
		$this->dateTo = $dateTo;
	}

	/**
	 * @return DateTime
	 */
	public function getTime() {
		return $this->time;
	}

	/**
	 * @param DateTime $time
	 */
	public function setTime($time) {
		$this->time = $time;
	}

	/**
	 * @return array
	 */
	public function extract() {
		return [
			'id' => $this->getId(),
			'article_id' => $this->getArticleId(),
			'date_from' => $this->getDateFrom(),
			'date_to' => $this->getDateTo(),
			'time' => $this->getTime()
		];
	}

	/**
	 * @return array
	 */
	public function extractForm() {
		return [
			'id' => $this->getId(),
			'article_id' => $this->getArticleId(),
			'date_from' => ($this->getDateFrom() != null ? $this->getDateFrom()->format("Y-m-d") : ""),
			'date_to' => ($this->getDateTo() != null ? $this->getDateTo()->format("Y-m-d") : ""),
			'time' => ($this->getTime() != null ? $this->getTime()->format("%h:%i") : "")
		];
	}

	/**
	 * @param array $data
	 */
	public function hydrate(array $data) {
		$this->setId(isset($data['id']) ? $data['id'] : null);
		$this->setArticleId(isset($data['article_id']) ? $data['article_id'] : null);
		$this->setDateFrom(isset($data['date_from']) ? $data['date_from'] : null);
		$this->setDateTo(isset($data['date_to']) ? $data['date_to'] : null);
		$this->setTime(isset($data['time']) ? $data['time'] : null);
	}
}