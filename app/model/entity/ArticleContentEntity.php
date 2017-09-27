<?php

namespace App\Model\Entity;

class ArticleContentEntity {

	/** @var int */
	private $id;

	/** @var int */
	private $articleId;

	/** @var string */
	private $header;

	/** @var string */
	private $lang;

	/** @var string */
	private $content;

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
	 * @param int $article_id
	 */
	public function setArticleId($articleId) {
		$this->articleId = $articleId;
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
	 * @return string
	 */
	public function getContent() {
		return $this->content;
	}

	/**
	 * @param string $content
	 */
	public function setContent($content) {
		$this->content = $content;
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
	 * @return array
	 */
	public function extract() {
		return [
			'id' => $this->getId(),
			'article_id' => $this->getArticleId(),
			'lang' => $this->getLang(),
			'content' => $this->getContent(),
			'header' => $this->getHeader()
		];
	}

	/**
	 * @param array $data
	 */
	public function hydrate(array $data) {
		$this->setId(isset($data['id']) ? $data['id'] : null);
		$this->setArticleId(isset($data['article_id']) ? $data['article_id'] : null);
		$this->setLang(isset($data['lang']) ? $data['lang'] : null);
		$this->setContent(isset($data['content']) ? $data['content'] : null);
		$this->setHeader(isset($data['header']) ? $data['header'] : null);
	}
}