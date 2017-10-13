<?php

namespace App\Model;

use App\Model\Entity\ArticleCategoryEntity;
use App\Model\Entity\ArticleContentEntity;
use App\Model\Entity\ArticleEntity;
use App\Model\Entity\ArticleTimetableEntity;
use App\Model\Entity\PicEntity;
use Dibi\Connection;
use Dibi\DateTime;
use Dibi\Row;

class ArticleRepository extends BaseRepository {

	/** Maska data v DB  */
	const DB_DATE_MASK = 'Y-m-d';
	const URL_DATE_MASK = 'd.m.Y';

	/** @var  PicRepository */
	private $picRepository;

	/** @var ArticleTimetableRepository */
	private $articleTimetableRepository;

	/** @var ArticleCategoryRepository */
	private $articleCategoryRepository;

	/** @var MenuRepository */
	private $menuRepository;

	/** @var Connection */
	protected $connection;

	/**
	 * ArticleRepository constructor.
	 * @param PicRepository $picRepository
	 * @param Connection $connection
	 */
	public function __construct(
		PicRepository $picRepository,
		ArticleTimetableRepository $articleTimetableRepository,
		Connection $connection,
		ArticleCategoryRepository $articleCategoryRepository,
		MenuRepository $menuRepository
	) {
		parent::__construct($connection);
		$this->articleTimetableRepository = $articleTimetableRepository;
		$this->picRepository = $picRepository;
		$this->articleCategoryRepository = $articleCategoryRepository;
		$this->menuRepository = $menuRepository;
	}

	/**
	 * @param string $lang
	 * @param null $type
	 * @return array
	 */
	public function findArticlesInLang($lang, $type = null, $filter = null) {
		if ($filter == null) {
			$query = ["select * from article as a "];
		} else {
			if (isset($filter['active']) && ($filter['active'] == 2)) {
				$filter['active'] = 0;
			}
			if (isset($filter['menuOrders'])) {
				$menuOrders = $filter['menuOrders'];
				unset($filter['menuOrders']);
				$query = ["select a.* from article as a left join article_category as ac on a.id = ac.article_id where menu_order in %in and %and", $menuOrders, $filter];
			} else {
				$query = ["select a.* from article as a where 1 and %and", $filter];
			}
		}
		$query[] = " order by inserted_timestamp desc";

		$result = $this->connection->query($query)->fetchAll();
		$articles = [];
		foreach ($result as $item) {
			$articleEntity = new ArticleEntity();
			$articleEntity->hydrate($item->toArray());
			$articleEntity->setContents($this->findArticleContents($articleEntity->getId()));
			$articleEntity->setTimetables($this->articleTimetableRepository->findCalendars($articleEntity->getId()));
			$articleEntity->setCategories($this->articleCategoryRepository->findCategories($articleEntity->getId()));

			if (($type == null) || (($type != null) && ($articleEntity->getType() == $type))) {
				$articles[] = $articleEntity;
			}
		}

		return $articles;
	}

	/**
	 * @param string $lang
	 * @return int
	 */
	public function getActiveArticlesInLangCount($lang, $type) {
		$articles = $this->findActiveArticlesInLang($lang, $type);
		return count($articles);
	}

	/**
	 * @param string $lang
	 * @param null $type
	 * @param int $paginatorLength
	 * @param int $offset
	 * @return array
	 */
	public function findActiveArticlesInLang($lang, $type = null, $paginatorLength = 0, $offset = 0) {
		if ($type == EnumerationRepository::TYP_PRISPEVKU_AKCE_ORDER) {
			$query = ["select distinct a.id, a.*, `at`.id as atId  
						from article_timetable as `at`
							left join article as a on `at`.article_id = a.id
							left join article_content as ac on `at`.article_id = ac.article_id
							 where `at`.date_from <= %s and `at`.date_to >= %s 
							  and ac.lang = %s
							  and a.active = 1",
				(new DateTime())->format(self::DB_DATE_MASK),
				(new DateTime())->format(self::DB_DATE_MASK),
				$lang
			];
		} else {
			$query = ["select a.*  
				from article as a 
					left join article_content as ac on a.id = ac.article_id
				where ac.lang = %s
					and active = 1", $lang];
		}
		if ($type != null) {
			$query[] = sprintf(" and a.type = %d", $type);
		}

		if ($type == EnumerationRepository::TYP_PRISPEVKU_AKCE_ORDER) {
			$query[] = " order by validity desc, `at`.time asc";
		} else {
			$query[] = " order by validity desc, inserted_timestamp desc";
		}
		if ($paginatorLength != 0 ) {
			$query[] = sprintf("limit %d offset %d", $paginatorLength, $offset);
		}

		return $this->findArticleBySql($query, $type);
	}

	/**
	 * @param string $lang
	 * @param array $categories
	 * @param string [$searchText]
	 * @param string [$type]
	 * @return mixed
	 */
	public function getActiveArticlesInLangCategoryCount($lang, array $categories, $searchText = null, $sublocation = null, $type = null) {
		$articles = $this->findActiveArticlesInLangCategory($lang, $categories, $searchText, $sublocation, $type);
		return count($articles);
	}

	/**
	 * @param string $lang
	 * @param array $categories
	 * @param null $searchText
	 * @param null $type
	 * @param int $paginatorLength
	 * @param int $offset
	 * @return array
	 */
	public function findActiveArticlesInLangCategory($lang, array $categories, $searchText = null, $sublocation = null, $type = null, $paginatorLength = 0, $offset = 0) {
		if ($type == EnumerationRepository::TYP_PRISPEVKU_AKCE_ORDER) {
			$query = ["select distinct a.id, a.*, `at`.id as atId 
						from article_timetable as `at`
							left join article as a on `at`.article_id = a.id
							left join article_content as ac on `at`.article_id = ac.article_id
							left join article_category as aca on a.id = aca.article_id
							 where `at`.date_from <= %s and `at`.date_to >= %s 
							  and ac.lang = %s
							  and a.active = 1
							  and `aca.menu_order` in %in",
				(new DateTime())->format(self::DB_DATE_MASK),
				(new DateTime())->format(self::DB_DATE_MASK),
				$lang,
				$categories
			];
		} else {
			$query = ["select distinct a.id, a.*    
						from article as a 
							left join article_content as ac on a.id = ac.article_id
							left join article_category as aca on a.id = aca.article_id 
						where ac.lang = %s
							and active = 1 
							and `aca.menu_order` in %in",
							$lang,
							$categories
						];
		}

		if ($searchText != null) {
			$query[] = sprintf(" and CONCAT_WS(' ',ac.header,ac.content) like  '%%%s%%'", $searchText);
		}
		if ($sublocation != null) {
			$query[] = sprintf(" and a.sublocation = %d", $sublocation);
		}
		if ($type != null) {
			$query[] = sprintf(" and a.type = %d", $type);
		}

		if ($type == EnumerationRepository::TYP_PRISPEVKU_AKCE_ORDER) {
			$query[] = " order by validity desc, TIME(`at`.time) asc";
		} else {
			$query[] = " order by validity desc, inserted_timestamp desc";
		}

		if ($paginatorLength != 0 ) {
			$query[] = sprintf("limit %d offset %d", $paginatorLength, $offset);
		}

		return $this->findArticleBySql($query, $type);
	}

	/**
	 * @param $lang
	 * @param \DateTime $dateFrom
	 * @param null $searchText
	 * @param \DateTime|null $dateTo
	 * @param int $type
	 * @param null $sublocation
	 * @return array
	 */
	public function findActiveArticlesInLangByDate(
			$lang,
			\DateTime $dateFrom,
			$searchText = null,
			\DateTime $dateTo = null,
			$type = EnumerationRepository::TYP_PRISPEVKU_AKCE_ORDER,
			$sublocation = null
	) {
		$dateFrom = ($dateFrom == null ? new DateTime() : $dateFrom);
		$query = ["select distinct a.id, a.*, `at`.id as atId 
					from article_timetable as `at`
						left join article as a on `at`.article_id = a.id
						left join article_content as ac on `at`.article_id = ac.article_id
						 where ac.lang = %s
					and active = 1", $lang];

		if (($dateFrom != null) && ($dateTo != null)) {
			$query[] = sprintf(" and (`at`.date_from <= '%s' and `at`.date_to >= '%s')", $dateTo->format(self::DB_DATE_MASK), $dateFrom->format(self::DB_DATE_MASK));
		} else {
			$query[] = sprintf(" and `at`.date_from <= '%s' and `at`.date_to >= '%s'", $dateFrom->format(self::DB_DATE_MASK), $dateFrom->format(self::DB_DATE_MASK));
		}
		if ($searchText != null) {
			$query[] = sprintf(" and CONCAT_WS(' ',ac.header,ac.content) like  '%%%s%%'", $searchText);
		}
		if ($type != null) {
			$query[] = sprintf(" and a.type = %d", $type);
		}
		if ($sublocation != null) {
			$query[] = sprintf(" and a.sublocation = %d", $sublocation);
		}

		if ($type == EnumerationRepository::TYP_PRISPEVKU_AKCE_ORDER) {
			$query[] = " order by validity desc, `at`.time asc";
		} else {
			$query[] = " order by validity desc, inserted_timestamp desc";
		}

		return $this->findArticleBySql($query, $type);
	}

	/**
	 * @param string $lang
	 * @param int $sublocation
	 * @param null $type
	 * @param bool $placeIsNull
	 * @return array
	 */
	public function findActiveArticleBySublocationInLang($lang, $sublocation, $type = null, $placeIsNull = true) {
		$places = [];
		if ($sublocation != null) {
			if ($type == EnumerationRepository::TYP_PRISPEVKU_AKCE_ORDER) {
				$query = ["select distinct a.*, `at`.id as atId 
							from article_timetable as `at`
								left join article as a on `at`.article_id = a.id
								left join article_content as ac on `at`.article_id = ac.article_id
								 where `at`.date_from <= %s and `at`.date_to >= %s 
								  and ac.lang = %s
								  and a.active = 1
								  and a.sublocation = %i",
							(new DateTime())->format(self::DB_DATE_MASK),
							(new DateTime())->format(self::DB_DATE_MASK),
							$lang,
							$sublocation];
			} else {
				$query = ["select a.*  
							from article as a 
								left join article_content as ac on a.id = ac.article_id
							where ac.lang = %s
								and active = 1
								and sublocation = %i",
						$lang,
						$sublocation];
			}
			if ($placeIsNull) {
				$query[] = " and a.place is null";
			}
			if ($type != null) {
				$query[] = sprintf(" and a.type = %d", $type);
			}

			if ($type == EnumerationRepository::TYP_PRISPEVKU_AKCE_ORDER) {
				$query[] = " order by validity desc, `at`.time asc";
			} else {
				$query[] = " order by validity desc, inserted_timestamp desc";
			}

			$places = $this->findArticleBySql($query, $type);
		}

		return $places;
	}

	/**
	 * @param string $lang
	 * @param int $place
	 * @param null $type
	 * @return array
	 */
	public function findActiveArticleByPlaceInLang($lang, $place, $type = null) {
		$places = [];
		if ($place != null) {
			if ($type == EnumerationRepository::TYP_PRISPEVKU_AKCE_ORDER) {
				$query = ["select distinct a.*, `at`.id as atId 
							from article_timetable as `at`
								left join article as a on `at`.article_id = a.id
								left join article_content as ac on `at`.article_id = ac.article_id
								 where `at`.date_from <= %s and `at`.date_to >= %s 
								  and ac.lang = %s
								  and a.active = 1
								  and a.place = %i",
					(new DateTime())->format(self::DB_DATE_MASK),
					(new DateTime())->format(self::DB_DATE_MASK),
					$lang,
					$place];
			} else {
				$query = ["select a.*  
							from article as a 
								left join article_content as ac on a.id = ac.article_id
							where ac.lang = %s
								and active = 1
								and place = %i",
					$lang,
					$place];
			}
			if ($type != null) {
				$query[] = sprintf(" and a.type = %d", $type);
			}

			if ($type == EnumerationRepository::TYP_PRISPEVKU_AKCE_ORDER) {
				$query[] = " order by validity desc, `at`.time asc";
			} else {
				$query[] = " order by validity desc, inserted_timestamp desc";
			}

			$places = $this->findArticleBySql($query, $type);
		}

		return $places;
	}

	/**
	 * @param null $type
	 * @return array
	 */
	public function findArticles($type = null) {
		$query = ["select * from article order by order by id desc"];

		$result = $this->connection->query($query)->fetchAll();
		$articles = [];
		foreach ($result as $item) {
			$articleEntity = new ArticleEntity();
			$articleEntity->hydrate($item->toArray());

			$articleContentsEntity = $this->findArticleContents($articleEntity->getId());
			$articleEntity->setContents($articleContentsEntity);
			$articleEntity->setTimetables($this->articleTimetableRepository->findCalendars($articleEntity->getId()));
			$articleEntity->setCategories($this->articleCategoryRepository->findCategories($articleEntity->getId()));

			if (($type == null) || (($type != null) && ($articleEntity->getType() == $type))) {
				$articles[] = $articleEntity;
			}
		}

		return $articles;
	}

	/**
	 * @param null $type
	 * @return array
	 */
	public function findActiveArticles($type = null) {
		$query = ["select * from article where active = 1 order by inserted_timestamp desc"];

		$result = $this->connection->query($query)->fetchAll();
		$articles = [];
		foreach ($result as $item) {
			$articleEntity = new ArticleEntity();
			$articleEntity->hydrate($item->toArray());

			$articleContentsEntity = $this->findArticleContents($articleEntity->getId());
			$articleEntity->setContents($articleContentsEntity);
			$articleEntity->setTimetables($this->articleTimetableRepository->findCalendars($articleEntity->getId()));
			$articleEntity->setCategories($this->articleCategoryRepository->findCategories($articleEntity->getId()));

			if (($type == null) || (($type != null) && ($articleEntity->getType() == $type))) {
				$articles[] = $articleEntity;
			}
		}

		return $articles;
	}

	/**
	 * Vráté entitu Article podle ID včetně obsahů
	 *
	 * @param int $id
	 * @return ArticleEntity
	 */
	public function getArticle($id) {
		$query = ["select * from article where id = %i", $id];
		$result = $this->connection->query($query)->fetch();
		if ($result) {
			$articleEntity = new ArticleEntity();
			$articleEntity->hydrate($result->toArray());

			$articleContentEntities = $this->findArticleContents($articleEntity->getId());
			$articleEntity->setContents($articleContentEntities);
			$articleEntity->setTimetables($this->articleTimetableRepository->findCalendars($articleEntity->getId()));
			$articleEntity->setCategories($this->articleCategoryRepository->findCategories($articleEntity->getId()));

			return $articleEntity;
		}
	}

	/**
	 * Vrátí pole pro editaci položky příspěvku
	 *
	 * @param int $id
	 * @return array
	 */
	public function getArticleForEdit($id) {
		$result = [];
		$articleEntity = $this->getArticle($id);
		if ($articleEntity) {
			$result = $articleEntity->extract();

			$articleContentEntities = $this->findArticleContents($articleEntity->getId());
			foreach ($articleContentEntities as $articleContentEntity) {
				$result[$articleContentEntity->getLang()] = $articleContentEntity->extract();
			}

			$articleCategories = $this->articleCategoryRepository->findCategories($articleEntity->getId());
			foreach ($articleCategories as $articleCategory) {
				$result['menuOrders'][$articleCategory->getMenuOrder()] = $articleCategory->getMenuOrder();
			}
		}

		return $result;
	}

	/**
	 * @param ArticleEntity $articleEntity
	 * @param $userId
	 * @param array $calendars
	 * @param array $categories
	 * @return bool
	 */
	public function saveCompleteArticle(ArticleEntity $articleEntity, $userId, array $calendars, array $categories, array $docFiles = []) {
		$result = true;
		try {
			$this->connection->begin();
			if ($articleEntity->getSublocation() == 0) {
				$articleEntity->setSublocation(null);
			}
			if ($articleEntity->getPicId() == 0) {
				$articleEntity->setPicId(null);
			}
			if ($articleEntity->getPlace() == 0) {
				$articleEntity->setPlace(null);
			}
			if ($articleEntity->getGalleryId() == 0) {
				$articleEntity->setGalleryId(null);
			}
			$articleId = $this->saveArticleEntity($articleEntity, $userId);
			if (empty($articleId)) {
				throw new \Exception("Chybí ID příspěvku.");
			}

			$this->articleTimetableRepository->deleteByArticleId($articleId);	// před uložením všechny smažu
			$this->saveArticleTimetable($calendars, $articleId);	// následně uložím znovu

			$this->articleCategoryRepository->deleteByArticleId($articleId);	// smažu všechny záznami
			$this->saveArtcileCategory($categories, $articleId);	// následně uložím znovu

			$this->saveArticleContent($articleEntity->getContents(), $articleId);
			/** @var PicEntity $doc */
			foreach ($docFiles as $doc) {
				$doc->setArticleId($articleId);
				$this->picRepository->save($doc);
			}
			$articleEntity->setId($articleId);
			$this->connection->commit();
		} catch (\Exception $e) {
			// dump($e); die;
			$this->connection->rollback();
			$result = false;
		}

		return $result;
	}

	/**
	 * Vrátí pole obsahuů článku podle ID článku
	 *
	 * @param int $artickeId
	 * @return ArticleContentEntity[] kde klíčem v poli je zkratka jazyka
	 */
	private function findArticleContents($artickeId) {
		$query = ["select * from article_content where article_id = %i", $artickeId];
		$articleContents = $this->connection->query($query)->fetchAll();
		$articleContentsEntity = [];
		foreach ($articleContents as $content) {
			$articleContentEntity = new ArticleContentEntity();
			$articleContentEntity->hydrate($content->toArray());

			$articleContentsEntity[$articleContentEntity->getLang()] = $articleContentEntity;
		}

		return $articleContentsEntity;
	}

	/**
	 * @param ArticleEntity $articleEntity
	 * @return int
	 */
	private function saveArticleEntity(ArticleEntity $articleEntity, $userId) {
		$articleEntity->setInsertedBy($userId);
		if ($articleEntity->getId() == null) {
			$articleEntity->setInsertedTimestamp(new DateTime());
			$query = ["insert into article", $articleEntity->extract()];
		} else {
			$currentArticleEntity = $this->getArticle($articleEntity->getId());
			$articleEntity->setInsertedTimestamp($currentArticleEntity->getInsertedTimestamp());
			$articleEntity->setInsertedBy($currentArticleEntity->getInsertedBy());
			$articleEntity->setShowCounter($currentArticleEntity->getShowCounter());
			$articleEntity->setClickCounter($currentArticleEntity->getClickCounter());
			$query = ["update article set ", $articleEntity->extract(), "where id=%i", $articleEntity->getId()];
		}
		$this->connection->query($query);

		return ($articleEntity->getId() == null ? $this->connection->getInsertId() : $articleEntity->getId());
	}

	/**
	 * @param ArticleTimetableEntity[] $timetable
	 * @param int $articleId
	 */
	private function saveArticleTimetable(array $timetables, $articleId) {
		/** @var ArticleTimetableEntity $timetable */
		foreach ($timetables as $timetable) {
			$timetable->setArticleId($articleId);
			$this->articleTimetableRepository->save($timetable);
		}
	}

	/**
	 * @param ArticleCategoryEntity[] $articleCategories
	 * @param int $articleId
	 */
	private function saveArtcileCategory(array $articleCategories, $articleId) {
		/** @var ArticleCategoryEntity $category */
		foreach ($articleCategories as $category) {
			$category->setArticleId($articleId);
			$this->articleCategoryRepository->save($category);
		}
	}

	/**
	 * @param ArticleContentEntity[] $articleContentEntities
	 * @param $articleId
	 */
	private function saveArticleContent(array $articleContentEntities, $articleId) {
		$query = ["select * from article_content where article_id = %i", $articleId];

		$result = $this->connection->query($query)->fetchAll();
		/** @var ArticleContentEntity $articleContentEntity */
		foreach ($articleContentEntities as $articleContentEntity) {
			$articleContentEntity->setArticleId($articleId);
			if ($result) {
				$query = ["update article_content set header = %s, content = %s
						  where `lang` = %s
						  	and `article_id` = %i",
					$articleContentEntity->getHeader(),
					$articleContentEntity->getContent(),
					$articleContentEntity->getLang(),
					$articleId
				];
			} else {
				$query = ["insert into article_content", $articleContentEntity->extract()];
			}
			$this->connection->query($query);
		}
	}

	/**
	 * @param int $id
	 * @return bool
	 */
	public function deleteArticle($id) {
		$result = true;
		try {
			$this->connection->begin();

			// nejdříve smažu obsahy
			$query = ["delete from article_content where article_id = %i", $id];
			$this->connection->query($query);

			// pak musím smazat položky kalendáře
			$this->articleTimetableRepository->deleteByArticleId($id);

			// pak smažu kategorie
			$this->articleCategoryRepository->deleteByArticleId($id);

			// pak smažu samotný příspěvek
			$query = ["delete from article where id = %i", $id];
			$this->connection->query($query);

			$this->connection->commit();
		} catch (\Exception $e) {
			$result = false;
			$this->connection->rollback();
		}

		return $result;
	}

	/**
	 * @param int $id
	 * @return \Dibi\Result|int
	 */
	public function setArticleActive($id) {
		$query = ["update article set active = 1 where id = %i", $id];
		return $this->connection->query($query);
	}

	/**
	 * @param int $id
	 * @return \Dibi\Result|int
	 */
	public function setArticleInactive($id) {
		$query = ["update article set active = 0 where id = %i", $id];
		return $this->connection->query($query);
	}

	/**
	 * @return array
	 */
	public function findAddresses() {
		$query = "select distinct address from article where address is not null";
		return $this->connection->query($query)->fetchPairs('address', 'address');
	}

	/**
	 * @param int $validity
	 * @param ArticleCategoryEntity[] $categories
	 * @return array
	 */
	public function findSliderPics($validity = EnumerationRepository::TYP_VALIDITY_TOP, array $categories = []) {
		if (empty($categories)) {
			$query = ["select distinct a.* from article as a 
				left join article_timetable as `at` on a.id = `at`.article_id 
				where a.validity = %i and a.active = 1 and 
				 if (a.type = %i,
					(
						((`at`.date_from <= CURDATE()) and ((`at`.date_to is null) or (`at`.date_to = '0000-00-00'))) 
						or ((`at`.date_from <= CURDATE()) and (`at`.date_to >= CURDATE()))
					),
					1)",
				$validity,
				EnumerationRepository::TYP_PRISPEVKU_AKCE_ORDER
			];
		} else {
			$cats = [];
			foreach ($categories as $cat) {
				 $cats[] = $cat->getMenuOrder();
			}
			$query = ["select distinct a.* from article as a 
				left join article_timetable as `at` on a.id = `at`.article_id 
				left join article_category as aca on a.id = aca.article_id
				where a.validity = %i and a.active = 1 and aca.menu_order in %in and
				 if (a.type = %i,
				  (
				  	((`at`.date_from <= CURDATE()) and ((`at`.date_to is null) or (`at`.date_to = '0000-00-00'))) 
					or ((`at`.date_from <= CURDATE()) and (`at`.date_to >= CURDATE()))
				   ),
				   1)",
				$validity,
				$cats,
				EnumerationRepository::TYP_PRISPEVKU_AKCE_ORDER
			];
		}

		$bannersOut = [];
		$result = $this->connection->query($query)->fetchAll();
		foreach ($result as $article) {
			$articleEntity = new ArticleEntity();
			$articleEntity->hydrate($article->toArray());

			$articleEntity->setTimetables($this->articleTimetableRepository->findCalendars($articleEntity->getId()));
			$articleEntity->setContents($this->findArticleContents($articleEntity->getId()));
			$articleEntity->setCategories($this->articleCategoryRepository->findCategories($articleEntity->getId()));
			// $this->articleShowed($articleEntity->getId()); // slider načítám vždy, mám to vůbec počítat pro slider?

			$bannersOut[] = $articleEntity;
		}

		return $bannersOut;
	}

	/**
	 * @param int $id
	 */
	public function articleClicked($id) {
		$query = ["update article set click_counter = click_counter + 1 where id = %i", $id];
		$this->connection->query($query);
	}

	/**
	 * @param int[] $ids
	 */
	public function articleShowedByIds(array $ids) {
		$query = ["update article set show_counter = show_counter + 1 where id in %in", $ids];
		$this->connection->query($query);
	}

	/**
	 * Vrátí emaily z pohledové tabulky
	 * @return array
	 */
	public function findEmailFromView() {
		$query = "select * from v_emails_from_articles";
		$result = $this->connection->query($query)->fetchAll();

		$emails = [];
		foreach ($result as $record) {
			$emails[] = $record['contact_email'];
		}

		return $emails;
	}

	/**
	 * Metoda, která nastaví akce jako neaktivní pokud už nemají platný program
	 * @param string $lang
	 */
	public function deactivateOldEvents($lang) {
		// nejprve najdu všechny akce, které jsou označeny jako aktivní
		$query = ["select id from article where type = %i and active = 1", EnumerationRepository::TYP_PRISPEVKU_AKCE_ORDER];
		$result = $this->connection->query($query)->fetchAll();
		$allActiveEvents = [];
		foreach ($result as $item) {
			$itemArray = $item->toArray();
			$allActiveEvents[$itemArray['id']] = $itemArray['id'];
		}

		// potom najdu všechny akce, které jsou označeny jako aktivní a maji k dnešku platný program
		$categories = $this->menuRepository->findAllCategoryOrders();	// hledám ve všech kategoriích
		$allValidArticle = $this->findActiveArticlesInLangCategory($lang, $categories);
		$allValidEvents = [];
		/** @var ArticleEntity $activeArticle */
		foreach ($allValidArticle as $activeArticle) {	// vyfiltruji jen akce
			if ($activeArticle->getType() == EnumerationRepository::TYP_PRISPEVKU_AKCE_ORDER) {
				$allValidEvents[$activeArticle->getId()] = $activeArticle->getId();
			}
		}
		// potom porovnám pole = výsledek je Id přísůpěvků (akcí), které nemají platný program
		$activeEventsWithoutValidProgram = array_diff($allActiveEvents, $allValidEvents);
		foreach ($activeEventsWithoutValidProgram as $endedEventArticleId) {
			$this->setArticleInactive($endedEventArticleId);
		}
	}

	/**
	 * @param int $id
	 */
	private function articleShowed($id) {
		$query = ["update article set show_counter = show_counter + 1 where id = %i", $id];
		$this->connection->query($query);
	}

	/**
	 * @param array|string $query
	 * @return array
	 */
	private function findArticleBySql(array $query, $type) {
		$result = $this->connection->query($query)->fetchAll();
		$articles = [];
		foreach ($result as $item) {
			$itemArray = $item->toArray();
			$articleEntity = new ArticleEntity();
			$articleEntity->hydrate($itemArray);
			$articleEntity->setContents($this->findArticleContents($articleEntity->getId()));
			if (isset($itemArray['atId'])) { // pokud mám atId (article_timetable.id) načtu data o programu přímo
				$timetableById = $this->articleTimetableRepository->getTimetable($itemArray['atId']);
				$articleEntity->setTimetables([$timetableById]);
			} else {	// pokud atId nemám, musím seřadit podle času manuálně
				$articleEntity->setTimetables($this->articleTimetableRepository->findCalendars($articleEntity->getId()));
			}
			$articleEntity->setCategories($this->articleCategoryRepository->findCategories($articleEntity->getId()));
			$articles[] = $articleEntity;
		}

		return $this->sortingArticlesByTakingTime($articles, $type);
	}

	/**
	 * @param ArticleEntity[] $articles
	 * @param int $type
	 * @return array
	 */
	private function sortingArticlesByTakingTime(array $articles, $type) {
		$presorted = [];
		$notEventArticles = [];
		foreach ($articles as $article) {
			if (($article->getType() == EnumerationRepository::TYP_PRISPEVKU_AKCE_ORDER) && ($type == null)) {
				$timetable = $this->articleTimetableRepository->getActiveTimetable($article->getId());
				if ($timetable != null) {
					$article->setTimetables([$timetable]);
					$presorted[] = [$timetable->getTime(), clone $article];
				}
			} else {
				$notEventArticles[] = $article;
			}
		}
		usort($presorted, function ($a, $b) {
			$t1 = strtotime($a[0]->format("%H:%I"));
			$t2 = strtotime($b[0]->format("%H:%I"));
			return $t1 - $t2;
		});
		$sorted = [];
		$showedIds = [];	// id příspěvků, které budou zobrazeny
		foreach ($presorted as $pre) {
			$sorted[] = $pre[1];
			$showedIds[] = $pre[1]->getId();
		}

		foreach ($notEventArticles as $article) {
			$sorted[] = $article;
			$showedIds[] = $article->getId();
		}
		$this->articleShowedByIds($showedIds);

		return $sorted;
	}

}