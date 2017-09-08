<?php

namespace App\Model\Entity;

class ArticleCategoryEntity {

	/** @var int */
	private $id;

	/** @var int */
	private $article_id;

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
			'article_id' => $this->getArticleId(),
			'menu_order' => $this->getMenuOrder()
		];
	}

	/**
	 * @param array $data
	 */
	public function hydrate(array $data) {
		$this->setId(isset($data['id']) ? $data['id'] : null);
		$this->setArticleId(isset($data['article_id']) ? $data['article_id'] : null);
		$this->setMenuOrder(isset($data['menu_order']) ? $data['menu_order'] : null);
	}
}