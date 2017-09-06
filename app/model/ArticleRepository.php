<?php

namespace App\Model;

use App\Model\Entity\ArticleContentEntity;
use App\Model\Entity\ArticleEntity;
use App\Model\Entity\ArticleTimetableEntity;
use Dibi\Connection;
use Dibi\DateTime;
use Dibi\Row;

class ArticleRepository extends BaseRepository {

	/** @var  PicRepository */
	private $picRepository;

	/** @var ArticleTimetableRepository */
	private $articleTimetableRepository;

	/** @var Connection */
	protected $connection;

	/**
	 * ArticleRepository constructor.
	 * @param PicRepository $picRepository
	 * @param Connection $connection
	 */
	public function __construct(PicRepository $picRepository, ArticleTimetableRepository $articleTimetableRepository, Connection $connection) {
		parent::__construct($connection);
		$this->articleTimetableRepository = $articleTimetableRepository;
		$this->picRepository = $picRepository;
	}

	/**
	 * @param string $lang
	 * @return array
	 */
	public function findArticlesInLang($lang) {
		$query = ["
			select *, a.id as aID, ac.article_id as acId 
			from article as a 
				left join article_content as ac on a.id = ac.article_id
			where ac.lang = %s
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

			$articles[] = $articleEntity;
		}

		return $articles;
	}

	/**
	 * @return array
	 */
	public function findArticles() {
		$query = ["select * from article"];

		$result = $this->connection->query($query)->fetchAll();
		$articles = [];
		foreach ($result as $item) {
			$articleEntity = new ArticleEntity();
			$articleEntity->hydrate($item->toArray());

			$articleContentsEntity = $this->findArticleContents($articleEntity->getId());
			$articleEntity->setContents($articleContentsEntity);
			$articleEntity->setTimetables($this->articleTimetableRepository->findCalendars($articleEntity->getId()));
			$articles[] = $articleEntity;
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
		}

		return $result;
	}

	/**
	 * @param ArticleEntity $articleEntity
	 * @return bool
	 */
	public function saveCompleteArticle(ArticleEntity $articleEntity, $userId, $calendars, array $blockPicsEntities = []) {
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

			$this->saveArticleContent($articleEntity->getContents(), $articleId);
			foreach ($blockPicsEntities as $picEnt) {
				$this->picRepository->save($picEnt);
			}
			$articleEntity->setId($articleId);
			$this->connection->commit();
		} catch (\Exception $e) {
			dump($e); die;
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
			$articleEntity->setViewsCount($currentArticleEntity->getViewsCount());
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

	public function deleteArticle($id) {
		$result = true;
		try {
			$this->connection->begin();

			// nejdříve smažu obsahy
			$query = ["delete from article_content where article_id = %i", $id];
			$this->connection->query($query);

			// pak musím smazat položky kalendáře
			$this->articleTimetableRepository->deleteByArticleId($id);

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
}