<?php

namespace App\Model;

use App\Model\Entity\ArticleTimetableEntity;

class ArticleTimetableRepository extends BaseRepository {

	/**
	 * @param ArticleTimetableEntity $articleTimetableEntity
	 * @return int
	 */
	public function save(ArticleTimetableEntity $articleTimetableEntity) {
		if ($articleTimetableEntity->getDateFrom() != "") {
			//if ($articleTimetableEntity->getId() == null) {
			$articleTimetableEntity->setId(null);	// protože před ukládáním vždy z té tabulky vše smažu tak vlastně neustále vkládám nové zázanym
			if ($articleTimetableEntity->getTime() == "") {
				$articleTimetableEntity->setTime(null);
			} else if ($articleTimetableEntity->getTime() instanceof \DateInterval) {
				$articleTimetableEntity->setTime($articleTimetableEntity->getTime()->format("%H:%I"));
			}
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
			 */
			// dump($articleTimetableEntity); $this->connection->test($query); die;
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

	/**
	 * @param int $id
	 * @return ArticleTimetableEntity
	 */
	public function getTimetable($id) {
		$query = ["select * from article_timetable where id = %i", $id];
		$result = $this->connection->query($query);

		if ($result) {
			$timetableEntity = new ArticleTimetableEntity();
			$timetableEntity->hydrate($result->fetch()->toArray());

			return $timetableEntity;
		}
	}

	/**
	 * Najde 1 aktivní rozvrh podle ID článku
	 * @param int $id
	 * @return ArticleTimetableEntity
	 */
	public function getActiveTimetable($articleId) {
		$query = ["select * from article_timetable as `at`
					 where (`at`.article_id = %i) and 
					 (
					 	   (((`at`.date_to is null) or (`at`.date_to = '0000-00-00'))) 
						or ((`at`.date_to >= CURDATE()))
					)",
				$articleId];
		$result = $this->connection->query($query)->fetch();
		if ($result) {
			$timetableEntity = new ArticleTimetableEntity();
			$timetableEntity->hydrate($result->toArray());

			return $timetableEntity;
		}
	}
}