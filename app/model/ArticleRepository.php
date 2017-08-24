<?php

namespace App\Model;

use App\Model\Entity\ArticleContentEntity;
use App\Model\Entity\ArticleEntity;
use Dibi\Connection;

class ArticleRepository extends BaseRepository {

	/** @var  PicRepository */
	private $picRepository;

	/** @var Connection */
	protected $connection;

	/**
	 * ArticleRepository constructor.
	 * @param PicRepository $picRepository
	 * @param Connection $connection
	 */
	public function __construct(PicRepository $picRepository, Connection $connection) {
		parent::__construct($connection);
		$this->picRepository = $picRepository;
	}

	/**
	 * @param string $lang
	 * @return array
	 */
	public function findArticlesInLang($lang) {
		$query = ["
			select *, a.id as aID, ac.article_id as acId from article as a left join article_content as ac on a.id = ac.article_id
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
			$articleEntity->setContents([$articleContentEntity->getLang() => $articleContentEntity]);

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

			$query = ["selct * from article_content where article_id = %i", $articleEntity->getId()];
			$articleContents = $this->connection->query($query)->fetchAll();
			$articleContentsEntity = [];
			foreach ($articleContents as $content) {
				$articleContentEntity = new ArticleContentEntity();
				$articleContentEntity->hydrate($content->toArray());

				$articleContentsEntity[$articleContentEntity->getLang()] = $articleContentEntity;
			}
			$articleEntity->setContents($articleContentsEntity);
			$articles[] = $articleEntity;
		}

		return $articles;
	}

	/**
	 * @param ArticleEntity $articleEntity
	 * @return bool
	 */
	public function saveCompleteArticle(ArticleEntity $articleEntity, $userId, array $blockPicsEntities = []) {
		$result = true;
		try {
			$this->connection->begin();
			$articleId = $this->saveArticleEntity($articleEntity, $userId);
			if (empty($articleId)) {
				throw new \Exception("Chybí ID příspěvku.");
			}

			$this->saveArticleContent($articleEntity->getContents(), $articleId);
			foreach ($blockPicsEntities as $picEnt) {
				$this->picRepository->save($picEnt);
			}

			$this->connection->commit();
		} catch (\Exception $e) {
			dump($e); die;
			$this->connection->rollback();
			$result = false;
		}

		return $result;
	}

	/**
	 * @param ArticleEntity $articleEntity
	 * @return int
	 */
	private function saveArticleEntity(ArticleEntity $articleEntity, $userId) {
		$articleEntity->setInsertedBy($userId);
		if ($articleEntity->getId() == null) {
			$query = ["insert into article", $articleEntity->extract()];
		} else {
			$query = ["update article set ", $articleEntity->extract(), "where id=%i", $articleEntity->getId()];
		}
		$this->connection->query($query);

		return ($articleEntity->getId() == null ? $this->connection->getInsertId() : $articleEntity->getId());
	}

	/**
	 * @param ArticleEntity[] $articleContentEntities
	 * @param $articleId
	 */
	private function saveArticleContent(array $articleContentEntities, $articleId) {
		$query = ["select * from article_content where article_id = %i", $articleId];

		$result = $this->connection->query($query)->fetchAll();
		/** @var ArticleContentEntity $articleContentEntity */
		foreach ($articleContentEntities as $articleContentEntity) {
			$articleContentEntity->setArticleId($articleId);
			if ($result) {
				$query = ["update article_content set ",  $articleContentEntity->extract(),
						  "where `lang` = %s
						  	and `article_id` = %i",
					$articleContentEntity->getLang(),
					$articleId
				];
			} else {
				$query = ["insert into article_content", $articleContentEntity->extract()];
			}
			$this->connection->query($query);
		}
	}
}