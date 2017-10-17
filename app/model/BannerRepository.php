<?php

namespace App\Model;

use App\Model\Entity\ArticleCategoryEntity;
use App\Model\Entity\BannerCategoryEntity;
use App\Model\Entity\BannerEntity;
use Dibi\DateTime;

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

	/**
	 * @param int $bannerType
	 * @param bool $showOnMainPage
	 * @param ArticleCategoryEntity[] $categories
	 * @return BannerEntity
	 */
	public function getBannerByType($bannerType, $showOnMainPage = true, array $categories = []) {
		if (empty($categories)) {
			$query = ["select * from banner where banner_type = %i and 
					show_on_main_page in %in and 
					(((date_start <= CURDATE()) and ((date_end is null) or (date_end = '0000-00-00'))) or ((date_start <= CURDATE()) and (date_end >= CURDATE())))",
				$bannerType,
				($showOnMainPage ? [1] : [0,1])		// pokud jen na main page tak jedna, pokud ne tak je to jedno
			];
		} else {
			$menuOrders = [];
			foreach ($categories as $category) {
				$menuOrders[] = $category->getMenuOrder();
			}
			$query = ["select b.* 
					from banner as b
						left join banner_category as bc on b.id = bc.banner_id
					where banner_type = %i and 
					bc.menu_order in %in and
					show_on_main_page in %in and 
					(((date_start <= CURDATE()) and ((date_end is null) or (date_end = '0000-00-00'))) or ((date_start <= CURDATE()) and (date_end >= CURDATE())))",
				$bannerType,
				$menuOrders,
				($showOnMainPage ? [1] : [0,1])
			];
		}
		$query[] = " order by click_counter ASC LIMIT 1";
		$result = $this->connection->query($query)->fetch();

		if ($result) {
			$bannerEntity = new BannerEntity();
			$bannerEntity->hydrate($result->toArray());
			$bannerEntity->setCategories($this->findBannerCategories($bannerEntity->getId()));

			$this->bannerShowed($bannerEntity->getId());

			return $bannerEntity;
		}
	}

	/**
	 * @param int $bannerType
	 * @param bool $showOnMainPage
	 * @return BannerEntity[]
	 */
	public function findBannersByType($bannerType, $showOnMainPage = true) {
		$query = [
			"select * from banner 
				where banner_type = %i and 
					show_on_main_page = %i and 
					(((date_start <= CURDATE()) and ((date_end is null) or (date_end = '0000-00-00'))) or ((date_start <= CURDATE()) and (date_end >= CURDATE())))",
			$bannerType,
			($showOnMainPage ? 1 : 0)
		];	// TODO

		$bannersOut = [];
		$result = $this->connection->query($query)->fetchAll();

		foreach ($result as $banner) {
			$bannerEntity = new BannerEntity();
			$bannerEntity->hydrate($banner->toArray());
			$bannerEntity->setCategories($this->findBannerCategories($bannerEntity->getId()));

			$this->bannerShowed($bannerEntity->getId());
			$bannersOut[] = $bannerEntity;
		}

		return $bannersOut;
	}

	/**
	 * @param int $id
	 */
	public function bannerShowed($id) {
		$bannerEntity = $this->getBanner($id);
		if ($bannerEntity != null) {
			$showedTimes = $bannerEntity->getShowCounter() + 1;
			$bannerEntity->setShowCounter($showedTimes);

			$this->save($bannerEntity);
		}
	}

	/**
	 * @param int $id
	 */
	public function bannerClicked($id) {
		$bannerEntity = $this->getBanner($id);
		if ($bannerEntity != null) {
			$clickedTimes = $bannerEntity->getClickCounter() + 1;
			$bannerEntity->setClickCounter($clickedTimes);

			$this->save($bannerEntity);
		}
	}

	/**
	 * @param BannerEntity $bannerEntity
	 * @param int $userId
	 * @return bool
	 */
	public function saveCompleteBanner(BannerEntity $bannerEntity, $userId = 0) {
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
	public function save(BannerEntity $bannerEntity, $userId = 0) {
		if ($userId != 0) {
			$bannerEntity->setUserId($userId);
		}
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