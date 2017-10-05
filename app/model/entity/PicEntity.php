<?php

namespace App\Model\Entity;

use App\Enum\SharedFileEnum;

class PicEntity {

	/** @var int  */
	private $id;

	/** @var string  */
	private $path;

	/** @var int */
	private $fileType;

	/** @var int */
	private $articleId;

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
	public function getPath() {
		return $this->path;
	}

	/**
	 * @param string $path
	 */
	public function setPath($path) {
		$this->path = $path;
	}

	/**
	 * @return int
	 */
	public function getFileType() {
		return $this->fileType;
	}

	/**
	 * @param int $fileType
	 */
	public function setFileType($fileType) {
		$this->fileType = $fileType;
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
	 * @param array $data
	 */
	public function hydrate($data) {
		$this->id = (isset($data['id']) ? $data['id'] : null);
		$this->path = $data['path'];
		$this->fileType = (isset($data['file_type']) ? $data['file_type'] : SharedFileEnum::PIC);
		$this->articleId = (isset($data['article_id']) ? $data['article_id'] : null);
	}

	/**
	 * @return array
	 */
	public function extract() {
		return [
			'id' => $this->id,
			'path' => $this->path,
			'file_type' => $this->fileType,
			'article_id' => $this->articleId
		];
	}

	/**
	 * Vrátí pouze nazev soubouru, splitováni probíhá pomocí datového razítka
	 */
	public function getFilename() {
		$result = "";
		$splited = preg_split("/[0-9]{8}-[0-9]{6}/", $this->path);
		if (count($splited) > 1) {
			$result = substr($splited[1], 1);
		}

		return $result;
	}
}