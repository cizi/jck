<?php

namespace App\Model;

use App\Enum\SharedFileEnum;
use App\Model\Entity\PicEntity;
use Dibi\Exception;

class PicRepository extends BaseRepository {

	/**
	 * @return PicEntity[]
	 */
	public function load() {
		$return = [];
		$query = ["select * from shared_pic where file_type = %i", SharedFileEnum::PIC];
		$result = $this->connection->query($query)->fetchAll();
		foreach ($result as $item) {
			$footerPic = new PicEntity();
			$footerPic->hydrate($item->toArray());
			$return[] = $footerPic;
		}

		return $return;
	}

	/**
	 * Vrátí obrázky pro select
	 *
	 * @return array
	 */
	public function loadForSelect() {
		$result[0] = EnumerationRepository::NOT_SELECTED;
		$pics = $this->load();
		foreach ($pics as $pic) {
			$result[$pic->getId()] = $pic->getPath();
		}

		return $result;
	}


	/**
	 * @param PicEntity $picEntity
	 * @return \Dibi\Result|int
	 */
	public function save(PicEntity $picEntity) {
		if ($picEntity->getFileType() == null) {
			$picEntity->setFileType(SharedFileEnum::PIC);
		}
		$query = ["insert into shared_pic", $picEntity->extract()];
		return $this->connection->query($query);
	}

	/**
	 * @param int $id
	 * @return \Dibi\Result|int
	 */
	public function delete($id) {
		$result = true;
		try {
			$query = ["delete from shared_pic where id = %i", $id];
			$this->connection->query($query);
		} catch (\Exception $e) {
			$result = false;
		}

		return $result;
	}

	/**
	 * @param int $id
	 * @return PicEntity
	 */
	public function getById($id) {
		$query = ["select * from shared_pic where id = %i", $id];
		$result = $this->connection->query($query)->fetch();
		$footerPic = new PicEntity();
		if ($result) {
			$footerPic->hydrate($result->toArray());
		}

		return $footerPic;
	}

	/**
	 * @param int $articleId
	 * @return array
	 */
	public function loadDocs($articleId) {
		$return = [];
		$query = ["select * from shared_pic where article_id = %i and file_type = %i", $articleId, SharedFileEnum::DOC];
		$result = $this->connection->query($query)->fetchAll();
		foreach ($result as $item) {
			$doc = new PicEntity();
			$doc->hydrate($item->toArray());
			$return[] = $doc;
		}

		return $return;
	}

}