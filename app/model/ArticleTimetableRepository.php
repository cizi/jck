<?php

namespace App\Model;

use App\Model\Entity\ArticleTimetableEntity;

class ArticleTimetableRepository extends BaseRepository {

	/**
	 * @param ArticleTimetableEntity $articleTimetableEntity
	 * @return int
	 */
	public function save(ArticleTimetableEntity $articleTimetableEntity) {
		if (
			($articleTimetableEntity->getDateFrom() != "")
			&& ($articleTimetableEntity->getTime() != "")
		) {

			//if ($articleTimetableEntity->getId() == null) {
			$articleTimetableEntity->setId(null);	// protože před ukládáním vždy z tété tabulky vše smažu tak vlastně neustále vkládám nové zázanym
			$query = ["insert into article_timetable", $articleTimetableEntity->extract()];
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
			dump($articleTimetableEntity);
			$this->connection->test($query); */
			$this->connection->query($query);
		}

		return ($articleTimetableEntity->getId() == null ? $this->connection->getInsertId() : $articleTimetableEntity->getId());
	}

	/**
	 * @param int $id
	 * @return \Dibi\Result|int
	 */
	public function delete($id) {
		$query = ["delete from article_timetable where id = %i", $id];
		return $this->connection->query($query);
	}

	/**
	 * @param int $articleId
	 * @return \Dibi\Result|int
	 */
	public function deleteByArticleId($articleId) {
		$query = ["delete from article_timetable where article_id = %i", $articleId];
		return $this->connection->query($query);
	}

	public function findCalendars($article_id) {
		$query = ["select * from article_timetable where article_id = %i", $article_id];

		$result = $this->connection->query($query)->fetchAll();
		$calendars = [];
		foreach($result as $item) {
			$articleTimetableEntity = new ArticleTimetableEntity();
			$articleTimetableEntity->hydrate($item->toArray());
			$calendars[$articleTimetableEntity->getId()] = $articleTimetableEntity;
		}

		return $calendars;
	}
}