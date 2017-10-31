<?php

namespace App\Model;

use App\Model\Entity\GalleryContentEntity;
use App\Model\Entity\GalleryEntity;
use App\Model\Entity\GalleryPicEntity;

class GalleryRepository extends BaseRepository {

	const SHOW_ON_MAIN_PAGE = 1;
	const DONT_SHOW_ON_MAIN_PAGE = 0;

	/**
	 * @param $lang
	 * @return GalleryEntity[]
	 */
	public function findGalleriesInLang($lang) {
		$galleries = [];
		$query = ["select * from gallery"];
		$result = $this->connection->query($query)->fetchAll();
		foreach ($result as $gallery) {
			$galleryEntity = new GalleryEntity();
			$galleryEntity->hydrate($gallery->toArray());
			$galleryEntity->setContents($this->findGalleryContentsInLang($galleryEntity->getId(), $lang));
			$galleryEntity->setPics($this->findGalleryPics($galleryEntity->getId()));
			$galleries[] = $galleryEntity;
		}

		return $galleries;
	}

	/**
	 * Hledá v galerii podle fulltextu v nadpise/obsahu daného jazyka
	 * @param string $lang
	 * @param null $fulltextSearch
	 * @return array
	 */
	public function findGalleriesInLangByQuery($lang, $fulltextSearch = null) {
		$galleries = [];
		$query = ["select g.* from gallery as g left join gallery_content as gc on g.id = gc.gallery_id where gc.lang = %s", $lang];
		if (!empty($fulltextSearch)) {
			$query[] = sprintf(" and CONCAT_WS(' ', gc.header, gc.desc) like '%%%s%%'", $fulltextSearch);
		}
		$result = $this->connection->query($query)->fetchAll();
		foreach ($result as $gallery) {
			$galleryEntity = new GalleryEntity();
			$galleryEntity->hydrate($gallery->toArray());
			$galleryEntity->setContents($this->findGalleryContentsInLang($galleryEntity->getId(), $lang));
			$galleryEntity->setPics($this->findGalleryPics($galleryEntity->getId()));
			$galleries[] = $galleryEntity;
		}

		return $galleries;
	}

	/**
	 * @param $lang
	 * @param bool $showOnMainPage
	 * @return GalleryEntity[]
	 */
	public function findActiveGalleriesInLang($lang, $showOnMainPage = false) {
		$galleries = [];
		if ($showOnMainPage) {
			$query = ["select * from gallery where `active` = 1 and on_main_page = 1 order by inserted_timestamp desc"];
		} else {
			$query = ["select * from gallery where `active` = 1 order by inserted_timestamp desc"];
		}
		$result = $this->connection->query($query)->fetchAll();
		foreach ($result as $gallery) {
			$galleryEntity = new GalleryEntity();
			$galleryEntity->hydrate($gallery->toArray());
			$galleryEntity->setContents($this->findGalleryContentsInLang($galleryEntity->getId(), $lang));
			$galleryEntity->setPics($this->findGalleryPics($galleryEntity->getId()));
			$galleries[] = $galleryEntity;
		}

		return $galleries;
	}

	/**
	 * @param int $id
	 * @return GalleryEntity
	 */
	public function getGallery($galleryId) {
		$query = ["select * from gallery where id = %i", $galleryId];
		$result = $this->connection->query($query)->fetch();
		if ($result) {
			$galleryEntity = new GalleryEntity();
			$galleryEntity->hydrate($result->toArray());
			$galleryEntity->setContents($this->findGalleryContents($galleryEntity->getId()));
			$galleryEntity->setPics($this->findGalleryPics($galleryEntity->getId()));
			return $galleryEntity;
		}
	}

	public function saveCompleteGallery(GalleryEntity $galleryEntity) {
		$result = true;
		try {
			$this->connection->begin();
			$galleryId = $this->saveGalleryEntity($galleryEntity);
			foreach ($galleryEntity->getContents() as $galleryContentEntity) {
				$this->saveGalleryContent($galleryContentEntity, $galleryId);
			}
			$this->deleteGalleryPics($galleryId);
			foreach ($galleryEntity->getPics() as $galleryPicEntity) {
				$this->saveGalleryPics($galleryPicEntity, $galleryId);
			}
			$this->connection->commit();
		} catch (\Exception $e) {
			$this->connection->rollback();
			$result = false;
		}

		return $result;
	}

	/**
	 * @param $galleryId
	 */
	public function delete($galleryId) {
		try {
			$this->connection->begin();
			$this->deleteGalleryPics($galleryId);

			$query = ["delete from gallery_content where gallery_id = %i", $galleryId];
			$this->connection->query($query);

			$query = ["delete from gallery where id = %i", $galleryId];
			$this->connection->query($query);

			$this->connection->commit();
		} catch (\Exception $e) {
			$this->connection->rollback();
		}
	}

	/**
	 * @param int $id
	 * @return \Dibi\Result|int
	 */
	public function setGalleryOnMainPage($id) {
		$query = ["update gallery set on_main_page = 1 where id = %i", $id];
		return $this->connection->query($query);
	}

	/**
	 * @param int $id
	 * @return \Dibi\Result|int
	 */
	public function setGalleryNotMainPage($id) {
		$query = ["update gallery set on_main_page = 0 where id = %i", $id];
		return $this->connection->query($query);
	}

	/**
	 * @param int $id
	 * @return \Dibi\Result|int
	 */
	public function setGalleryActive($id) {
		$query = ["update gallery set active = 1 where id = %i", $id];
		return $this->connection->query($query);
	}

	/**
	 * @param int $id
	 * @return \Dibi\Result|int
	 */
	public function setGalleryInactive($id) {
		$query = ["update gallery set active = 0 where id = %i", $id];
		return $this->connection->query($query);
	}

	/**
	 * @param int $galleryId
	 */
	private function deleteGalleryPics($galleryId) {
		$query = ["delete from gallery_pic where gallery_id = %i", $galleryId];
		$this->connection->query($query);
	}

	/**
	 * @param int $id
	 * @param string $lang
	 * @return GalleryContentEntity[]
	 */
	private function findGalleryContentsInLang($id, $lang) {
		$contents = [];
		$query = ["select * from gallery_content as gc where gallery_id = %i and lang = %s", $id, $lang];
		$result = $this->connection->query($query)->fetchAll();
		foreach($result as $content) {
			$galleryContentEntity = new GalleryContentEntity();
			$galleryContentEntity->hydrate($content->toArray());
			$contents[$galleryContentEntity->getLang()] = $galleryContentEntity;
		}

		return $contents;
	}

	/**
	 * @param int $id
	 * @return GalleryContentEntity[]
	 */
	private function findGalleryContents($galleryId) {
		$contents = [];
		$query = ["select * from gallery_content where gallery_id = %i", $galleryId];
		$result = $this->connection->query($query)->fetchAll();
		foreach($result as $content) {
			$galleryContentEntity = new GalleryContentEntity();
			$galleryContentEntity->hydrate($content->toArray());
			$contents[$galleryContentEntity->getLang()] = $galleryContentEntity;
		}

		return $contents;
	}

	/**
	 * @param int $id
	 * @return GalleryPicEntity[]
	 */
	private function findGalleryPics($galleryId) {
		$pics = [];
		$query = ["select * from gallery_pic where gallery_id = %i", $galleryId];
		$result = $this->connection->query($query)->fetchAll();

		foreach	($result as $pic) {
			$galleryPicEntity = new GalleryPicEntity();
			$galleryPicEntity->hydrate($pic->toArray());
			$pics[$galleryPicEntity->getId()] = $galleryPicEntity;
		}

		return $pics;
	}

	/**
	 * @param GalleryEntity $galleryEntity
	 * @return int
	 */
	private function saveGalleryEntity(GalleryEntity $galleryEntity) {
		if ($galleryEntity->getOnMainPage() == null) {
			$galleryEntity->setOnMainPage(self::DONT_SHOW_ON_MAIN_PAGE);
		}
		if ($galleryEntity->getId() == null) {
			$query = ["insert into gallery", $galleryEntity->extract()];
		} else {
			$query = ["
			update gallery set `active` = %i, `on_main_page` = %i
			where id = %i",
				($galleryEntity->isActive() ? "1" : "0"),
				($galleryEntity->getOnMainPage() ? 1 : 0),
				$galleryEntity->getId()
			];
		}
		$this->connection->query($query);

		return ($galleryEntity->getId() == null ? $this->connection->getInsertId() : $galleryEntity->getId());
	}

	/**
	 * @param GalleryContentEntity $galleryContentEntity
	 * @param int $galleryId
	 * @return int
	 */
	private function saveGalleryContent(GalleryContentEntity $galleryContentEntity, $galleryId) {
		if ($galleryContentEntity->getId() == null) {
			$galleryContentEntity->setGalleryId($galleryId);
			$query = ["insert into gallery_content", $galleryContentEntity->extract()];
		} else {
			$query = ["
			update gallery_content set `header` = %s, `desc` = %s
			where id = %i",
				$galleryContentEntity->getHeader(),
				$galleryContentEntity->getDesc(),
				$galleryContentEntity->getId()
			];
		}
		$this->connection->query($query);

		return ($galleryContentEntity->getId() == null ? $this->connection->getInsertId() : $galleryContentEntity->getId());
	}

	/**
	 * @param GalleryPicEntity $galleryPicEntity
	 * @param int $galleryId
	 * @return int
	 */
	private function saveGalleryPics(GalleryPicEntity $galleryPicEntity, $galleryId) {
		$galleryPicEntity->setGalleryId($galleryId);
		$galleryPicEntity->setId(null);
		$query = ["insert into gallery_pic", $galleryPicEntity->extract()];
		$this->connection->query($query);

		return ($galleryPicEntity->getId() == null ? $this->connection->getInsertId() : $galleryPicEntity->getId());
	}
}