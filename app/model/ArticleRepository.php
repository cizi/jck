<?php

namespace App\Model;

use App\Model\Entity\ArticleContentEntity;
use App\Model\Entity\ArticleEntity;

class ArticleRepository extends BaseRepository {

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
			$articleEntity->setContents([$articleContentEntity]);

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
				$articleContentEntity->hydrate($item->toArray());

				$articleContentsEntity[] = $articleContentEntity;
			}
			$articleEntity->setContents($articleContentsEntity);
			$articles[] = $articleEntity;
		}

		return $articles;
	}
}