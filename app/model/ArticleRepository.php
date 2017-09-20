<?php

namespace App\Model;

use App\Model\Entity\ArticleCategoryEntity;
use App\Model\Entity\ArticleContentEntity;
use App\Model\Entity\ArticleEntity;
use App\Model\Entity\ArticleTimetableEntity;
use Dibi\Connection;
use Dibi\DateTime;
use Dibi\Row;

class ArticleRepository extends BaseRepository {

	/**
	 * Maska data v DB
	 */
	const DB_DATE_MASK = 'Y-m-d';

	const URL_DATE_MASK = 'd.m.Y';

	/** @var  PicRepository */
	private $picRepository;

	/** @var ArticleTimetableRepository */
	private $articleTimetableRepository;

	/** @var ArticleCategoryRepository */
	private $articleCategoryRepository;

	/** @var Connection */
	protected $connection;

	/**
	 * ArticleRepository constructor.
	 * @param PicRepository $picRepository
	 * @param Connection $connection
	 */
	public function __construct(PicRepository $picRepository, ArticleTimetableRepository $articleTimetableRepository, Connection $connection, ArticleCategoryRepository $articleCategoryRepository) {
		parent::__construct($connection);
		$this->articleTimetableRepository = $articleTimetableRepository;
		$this->picRepository = $picRepository;
		$this->articleCategoryRepository = $articleCategoryRepository;
	}

	/**
	 * @param string $lang
	 * @param null $type
	 * @return array
	 */
	public function findArticlesInLang($lang, $type = null) {
		$query = ["
			select *, a.id as aID, ac.article_id as acId 
			from article as a 
				left join article_content as ac on a.id = ac.article_id
			where ac.lang = %s
			order by inserted_timestamp desc
			",
			$lang
		];

		$result = $this->connection->query($query)->fetchAll();
		$articles = [];
		foreach ($result as $item) {
			$articleContentEntity = new ArticleContentEntity();
			$articleContentEntity->hydrate($item->toArray());
			$articleContentEntity->setId($item['acId']);

			$articleEntity = new ArticleEntity();
			$articleEntity->hydrate($item->toArray());
			$articleEntity->setId($item['aID']);
			$articleEntity->setContents([$articleContentEntity->getLang() => $articleContentEntity]);
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
		$query = ["
			select count(a.id) as articleCount
			from article as a 
				left join article_content as ac on a.id = ac.article_id
			where ac.lang = %s
				and active = 1
				and a.type = %i
			order by inserted_timestamp desc
			",
			$lang,
			$type
		];

		return $this->connection->query($query)->fetchSingle();
	}

	/**
	 * @param string $lang
	 * @param null $type
	 * @param int $paginatorLength
	 * @param int $offset
	 * @return array
	 */
	public function findActiveArticlesInLang($lang, $type = null, $paginatorLength = 0, $offset = 0) {
		$query = ["
			select *, a.id as aID, ac.article_id as acId 
			from article as a 
				left join article_content as ac on a.id = ac.article_id
			where ac.lang = %s
				and active = 1", $lang];

		if ($type != null) {
			$query[] = sprintf(" and a.type = %d", $type);
		}
		$query[] = "order by inserted_timestamp desc";
		if ($paginatorLength != 0 ) {
			$query[] = sprintf("limit %d offset %d", $paginatorLength, $offset);

		}

		$result = $this->connection->query($query)->fetchAll();
		$articles = [];
		foreach ($result as $item) {
			$articleContentEntity = new ArticleContentEntity();
			$articleContentEntity->hydrate($item->toArray());
			$articleContentEntity->setId($item['acId']);

			$articleEntity = new ArticleEntity();
			$articleEntity->hydrate($item->toArray());
			$articleEntity->setId($item['aID']);
			$articleEntity->setContents([$articleContentEntity->getLang() => $articleContentEntity]);
			$articleEntity->setTimetables($this->articleTimetableRepository->findCalendars($articleEntity->getId()));
			$articleEntity->setCategories($this->articleCategoryRepository->findCategories($articleEntity->getId()));

			$articles[] = $articleEntity;
		}

		return $articles;
	}

	public function getActiveArticlesInLangCategoryCount($lang, array $categories, $searchText = null, $type = null) {
		$query = ["
			select count(a.id) as articleCount   
			from article as a 
				left join article_content as ac on a.id = ac.article_id
				left join article_category as aca on a.id = aca.article_id 
			where ac.lang = %s
				and active = 1 
				and `aca.menu_order` in %in",
			$lang,
			$categories];

		if ($searchText != null) {
			$query[] = sprintf(" and CONCAT_WS(' ',ac.header,ac.content) like '%%%s%%'", $searchText);
		}
		if ($type != null) {
			$query[] = sprintf(" and a.type = %d", $type);
		}

		return  $this->connection->query($query)->fetchSingle();
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
		$query = ["
			select *, a.id as aID, ac.article_id as acId   
			from article as a 
				left join article_content as ac on a.id = ac.article_id
				left join article_category as aca on a.id = aca.article_id 
			where ac.lang = %s
				and active = 1 
				and `aca.menu_order` in %in",
			$lang,
			$categories];

		if ($searchText != null) {
			$query[] = sprintf(" and CONCAT_WS(' ',ac.header,ac.content) like  '%%%s%%'", $searchText);
		}
		if ($sublocation != null) {
			$query[] = sprintf(" and a.sublocation = %d", $sublocation);
		}
		if ($type != null) {
			$query[] = sprintf(" and a.type = %d", $type);
		}
		$query[] = "order by inserted_timestamp desc";
		if ($paginatorLength != 0 ) {
			$query[] = sprintf("limit %d offset %d", $paginatorLength, $offset);

		}

		$result = $this->connection->query($query)->fetchAll();
		$articles = [];
		foreach ($result as $item) {
			$articleContentEntity = new ArticleContentEntity();
			$articleContentEntity->hydrate($item->toArray());
			$articleContentEntity->setId($item['acId']);

			$articleEntity = new ArticleEntity();
			$articleEntity->hydrate($item->toArray());
			$articleEntity->setId($item['aID']);
			$articleEntity->setContents([$articleContentEntity->getLang() => $articleContentEntity]);
			$articleEntity->setTimetables($this->articleTimetableRepository->findCalendars($articleEntity->getId()));
			$articleEntity->setCategories($this->articleCategoryRepository->findCategories($articleEntity->getId()));

			$articles[] = $articleEntity;
		}

		return $articles;
	}

	/**
	 * @param $lang
	 * @param \DateTime $dateFrom
	 * @param null $searchText
	 * @param null $dateTo
	 * @param int $type
	 * @return array
	 */
	public function findActiveArticlesInLangByDate($lang, \DateTime $dateFrom, $searchText = null, \DateTime $dateTo = null, $type = EnumerationRepository::TYP_PRISPEVKU_AKCE_ORDER) {
		$dateFrom = ($dateFrom == null ? new DateTime() : $dateFrom);
		$query = ["select distinct a.id, a.* 
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
		$query[] = "order by inserted_timestamp desc";

		$result = $this->connection->query($query)->fetchAll();
		$articles = [];
		foreach ($result as $item) {
			$articleEntity = new ArticleEntity();
			$articleEntity->hydrate($item->toArray());
			$articleEntity->setContents($this->findArticleContents($articleEntity->getId()));
			$articleEntity->setTimetables($this->articleTimetableRepository->findCalendars($articleEntity->getId()));
			$articleEntity->setCategories($this->articleCategoryRepository->findCategories($articleEntity->getId()));

			$articles[] = $articleEntity;
		}

		return $articles;
	}

	/**
	 * @param string $lang
	 * @param int $sublocation
	 * @param null $type
	 * @return array
	 */
	public function findActiveArticleByPlaceInLang($lang, $sublocation, $type = null) {
		$places = [];
		if ($sublocation != null) {
			$query = [
				"
			select *, a.id as aID, ac.article_id as acId 
			from article as a 
				left join article_content as ac on a.id = ac.article_id
			where ac.lang = %s
				and active = 1
				and sublocation = %i",
				$lang,
				$sublocation
			];

			if ($type != null) {
				$query[] = sprintf(" and a.type = %d", $type);
			}
			$query[] = "order by inserted_timestamp desc";

			$result = $this->connection->query($query)->fetchAll();
			foreach ($result as $item) {
				$articleContentEntity = new ArticleContentEntity();
				$articleContentEntity->hydrate($item->toArray());
				$articleContentEntity->setId($item['acId']);

				$articleEntity = new ArticleEntity();
				$articleEntity->hydrate($item->toArray());
				$articleEntity->setId($item['aID']);
				$articleEntity->setContents([$articleContentEntity->getLang() => $articleContentEntity]);
				$articleEntity->setTimetables($this->articleTimetableRepository->findCalendars($articleEntity->getId()));
				$articleEntity->setCategories($this->articleCategoryRepository->findCategories($articleEntity->getId()));

				$places[] = $articleEntity;
			}
		}

		return $places;
	}



	/**
	 * @param null $type
	 * @return array
	 */
	public function findArticles($type = null) {
		$query = ["select * from article order by inserted_timestamp desc"];

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
	 * @param array $blockPicsEntities
	 * @return bool
	 */
	public function saveCompleteArticle(ArticleEntity $articleEntity, $userId, array $calendars, array $categories, array $blockPicsEntities = []) {
		$result = true;
		try {
			$this->connection->begin();
			if ($articleEntity->getSublocation() == 0) {
				$articleEntity->setSublocation(null);
			}
			if ($articleEntity->getPicId() == 0) {
				$articleEntity->setPicId(null);
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
			foreach ($blockPicsEntities as $picEnt) {
				$this->picRepository->save($picEnt);
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
				$query = ["update article_content set header = %s, seo = %s, content = %s
						  where `lang` = %s
						  	and `article_id` = %i",
					$articleContentEntity->getHeader(),
					$articleContentEntity->getSeo(),
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
	 * @param string $lang
	 * @return array
	 */
	public function findArticlesForSelect($lang) {
		$result[0] = EnumerationRepository::NOT_SELECTED;
		$query = ["select a.*, ac.header from article as a left join article_content as ac on a.id = ac.article_id where lang = %s", $lang];
		$queryResult = $this->connection->query($query)->fetchAll();

		foreach ($queryResult as $item) {
			$result[$item['id']] = $item['header'];
		}

		return $result;
	}

	/**
	 * @return array
	 */
	public function findAddresses() {
		$query = "select distinct address from article where address is not null";
		return $this->connection->query($query)->fetchPairs('address', 'address');
	}

	/**
	 * @param int $bannerType
	 * @param bool $showOnMainPage
	 * @return ArticleEntity[]
	 */
	public function findSliderPics($validity = EnumerationRepository::TYP_VALIDITY_TOP) {
		$query = [
			"select distinct a.*, a.id as aid, `at`.id as atid, `at`.date_from, `at`.date_to, `at`.time from article as a 
				left join article_timetable as `at` on a.id = `at`.article_id 
				where a.validity = %i and a.active = 1 and (
					((`at`.date_from <= CURDATE()) and ((`at`.date_to is null) or (`at`.date_to = '0000-00-00'))) 
					or ((`at`.date_from <= CURDATE()) and (`at`.date_to >= CURDATE()))
				)",
			$validity
		];

		$bannersOut = [];
		$result = $this->connection->query($query)->fetchAll();

		foreach ($result as $article) {
			$articleEntity = new ArticleEntity();
			$arr = $article->toArray();
			$articleEntity->hydrate($article->toArray());
			$articleEntity->setId(isset($arr['aid']) ? $arr['aid'] : null);

			$articleTimeTable = new ArticleTimetableEntity();
			$articleTimeTable->hydrate($arr);
			$articleTimeTable->setId(isset($arr['atid']) ? $arr['atid'] : null);
			$articleTimeTable->setArticleId(isset($arr['aid']) ? $arr['aid'] : null);

			$articleEntity->setTimetables([$articleTimeTable]);
			$articleEntity->setContents($this->findArticleContents($articleEntity->getId()));
			$articleEntity->setCategories($this->articleCategoryRepository->findCategories($articleEntity->getId()));
			$this->articleShowed($articleEntity->getId());

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
	 * @param int $id
	 */
	private function articleShowed($id) {
		$query = ["update article set show_counter = show_counter + 1 where id = %i", $id];
		$this->connection->query($query);
	}
}