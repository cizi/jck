<?php

namespace App\Model;

use App\Model\Entity\ArticleCategoryEntity;
use App\Model\Entity\ArticleTimetableEntity;

class ArticleCategoryRepository extends BaseRepository {

	/**
	 * @param ArticleCategoryEntity $articleCategoryEntity
	 * @return int
	 */
	public function save(ArticleCategoryEntity $articleCategoryEntity) {
		$articleCategoryEntity->setId(null);	// protože před ukládáním vždy z té tabulky vše smažu tak vlastně neustále vkládám nové zázanym
		$query = ["insert into article_category", $articleCategoryEntity->extract()];
		/*} else {
			$query = [
				"
			update article_timetable set `article_id` = %i,
			  `date_from` = %d,
			  `date_to` = %d,
			  `time` = %s
			where id = %i",
				$articleTimetableEntity->getArticleId(),
				$articleTimetableEntity->getDateFrom(),
				$articleTimetableEntity->getDateTo(),
				$articleTimetableEntity->getTime(),
				$articleTimetableEntity->getId()
			];
		}
		 */
		// dump($articleTimetableEntity); $this->connection->test($query); die;
		$this->connection->query($query);

		return ($articleCategoryEntity->getId() == null ? $this->connection->getInsertId() : $articleCategoryEntity->getId());
	}

	/**
	 * @param int $id
	 * @return \Dibi\Result|int
	 */
	public function delete($id) {
		$query = ["delete from article_category where id = %i", $id];
		return $this->connection->query($query);
	}

	/**
	 * @param int $articleId
	 * @return \Dibi\Result|int
	 */
	public function deleteByArticleId($articleId) {
		$query = ["delete from article_category where article_id = %i", $articleId];
		return $this->connection->query($query);
	}

	/**
	 * @param int $article_id
	 * @return ArticleCategoryEntity[]
	 */
	public function findCategories($article_id) {
		$query = ["select * from article_category where article_id = %i", $article_id];

		$result = $this->connection->query($query)->fetchAll();
		$categories = [];
		foreach($result as $item) {
			$articleCategoryEntity = new ArticleCategoryEntity();
			$articleCategoryEntity->hydrate($item->toArray());

			$categories[$articleCategoryEntity->getId()] = $articleCategoryEntity;
		}

		return $categories;
	}
}