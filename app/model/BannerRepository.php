<?php

namespace App\Model;

use App\Model\Entity\BannerCategoryEntity;
use App\Model\Entity\BannerEntity;

class BannerRepository extends BaseRepository {

	/**
	 * @param int $bannerId
	 * @return BannerEntity
	 */
	public function getBanner($bannerId) {
		$query = ["select * from banner where id = %i", $bannerId];
		$result = $this->connection->query($query);

		if ($result) {
			$bannerEntity = new BannerEntity();
			$bannerEntity->hydrate($result->fetch()->toArray());
			$bannerEntity->setCategories($this->findBannerCategories($bannerId));

			return $bannerEntity;
		}
	}

	/**
	 * @return BannerEntity[]
	 */
	public function findBanners() {
		$query = "select * from banner order by id desc";
		$result = $this->connection->query($query);

		$return = [];
		foreach($result->fetchAll() as $bannerData) {
			$banner = new BannerEntity();
			$banner->hydrate($bannerData->toArray());

			$categoryEntities = $this->findBannerCategories($banner->getId());
			$banner->setCategories($categoryEntities);

			$return[] = $banner;
		}

		return $return;
	}

	public function saveCompleteBanner(BannerEntity $bannerEntity, $userId) {
		$result = true;
		try {
			$this->connection->begin();
			$bannerId = $this->save($bannerEntity, $userId);

			$this->deleteCategories($bannerId);	// smažu kategorie
			foreach ($bannerEntity->getCategories() as $category) {
				$this->saveCategory($category, $bannerId);
			}

			$this->connection->commit();
		} catch (\Exception $e) {
			// dump($e);
			$result = false;
			$this->connection->rollback();
		}

		return $result;
	}

	/**
	 * @param BannerEntity $bannerEntity
	 * @return int
	 */
	public function save(BannerEntity $bannerEntity, $userId) {
		$bannerEntity->setUserId($userId);
		if ($bannerEntity->getId() == null) {
			$query = ["insert into banner", $bannerEntity->extract()];
		} else {
			$query = [" update banner set ", $bannerEntity->extract(), " where id = %i", $bannerEntity->getId()];
		}
		$this->connection->query($query);

		return ($bannerEntity->getId() == null ? $this->connection->getInsertId() : $bannerEntity->getId());
	}

	/**
	 * @param BannerCategoryEntity $bannerCategoryEntity
	 * @param $categoryId
	 */
	public function saveCategory(BannerCategoryEntity $bannerCategoryEntity, $bannerId) {
		$bannerCategoryEntity->setBannerId($bannerId);
		if ($bannerCategoryEntity->getId() == null) {
			$query = ["insert into banner_category", $bannerCategoryEntity->extract()];
		} else {
			$query = [" update banner_category set ", $bannerCategoryEntity->extract(), " where id = %i", $bannerCategoryEntity->getId()];
		}
		$this->connection->query($query);

		return ($bannerCategoryEntity->getId() == null ? $this->connection->getInsertId() : $bannerCategoryEntity->getId());
	}

	/**
	 * @param int $bannerId
	 * @return bool
	 */
	public function delete($bannerId) {
		$result = true;
		try {
			$this->connection->begin();
			$this->deleteCategories($bannerId);

			$query = ["delete from banner where id = %i", $bannerId];
			$this->connection->query($query);

			$this->connection->commit();
		} catch (\Exception $e) {
			$result = false;
			$this->connection->rollback();
		}

		return $result;
	}

	/**
	 * @param int $bannerId
	 */
	public function deleteCategories($bannerId) {
		$query = ["delete from banner_category where banner_id = %i", $bannerId];
		$this->connection->query($query);
	}

	/**
	 * @param int $id
	 * @return \Dibi\Result|int
	 */
	public function setBannerActiveOnMainPage($id) {
		$query = ["update banner set show_on_main_page = 1 where id = %i", $id];
		return $this->connection->query($query);
	}

	/**
	 * @param int $id
	 * @return \Dibi\Result|int
	 */
	public function setBannerInactiveOnMainPage($id) {
		$query = ["update banner set show_on_main_page = 0 where id = %i", $id];
		return $this->connection->query($query);
	}

	/**
	 * @param int $bannerId
	 * @return BannerCategoryEntity[]
	 */
	public function findBannerCategories($bannerId) {
		$query = ["select * from banner_category where banner_id = %i", $bannerId];
		$categories = $this->connection->query($query)->fetchAll();
		$categoryEntities = [];
		foreach ($categories as $category) {
			$bannerCategory= new BannerCategoryEntity();
			$bannerCategory->hydrate($category->toArray());
			$categoryEntities[] = $bannerCategory;
		}

		return $categoryEntities;
	}
}