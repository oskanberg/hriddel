<?php

class ReviewMapper extends AbstractDataMapper
{
    public function createNew(array $data)
    {
        if (!is_null($data)) {
            $new_review = new Review();
            $new_review->content = $data['content'];
            $new_review->authors = $data['authors'];
            $new_review->title = $data['title'];
            $new_review->type = $data['type'];
            $new_review->status = $data['status'];
            $new_review->cover_image = $data['cover_image'];
            $new_review->review_score = $data['review_score'];
            $new_review->date = $data['publish_date'];
            return $new_review;
        } else {
            throw new Exception('Need data.');
        }
    }

    public function save(AbstractObject $obj)
    {
        $obj->set_id($this->_save_to_database($obj));
    }

    public function delete(AbstractObject $obj)
    {

    }

    public function update(AbstractObject $obj)
    {
        try
        {
            $this->_database_connection->connect();
            $stmt = 'UPDATE articles SET content=:content, status=:status, title=:title, cover_image=:cover_image WHERE a_id=:a_id';
            $statement = $this->_database_connection->get_connection()->prepare($stmt);
            $statement->execute(array(
                ':content' => $obj->content,
                ':status' => $obj->status,
                ':title' => $obj->title,
                ':cover_image' => $obj->cover_image,
                ':a_id' => $obj->get_id(),
            ));

            $stmt = 'UPDATE review_scores SET score=:score WHERE a_id=:a_id';
            $statement = $this->_database_connection->get_connection()->prepare($stmt);
            $statement->execute(array(
                ':score' => $obj->review_score,
                ':a_id' => $obj->get_id(),
            ));
            $this->_database_connection->close_connection();
        } catch (PDOException $e) {
            $this->_database_connection->close_connection();
            echo 'ERROR: ' . $e->getMessage();
        }
    }

    public function findById($id)
    {
        $this->_database_connection->connect();
        try
        {
            $stmt = 'SELECT * FROM articles WHERE a_id=:a_id';
            $statement = $this->_database_connection->get_connection()->prepare($stmt);
            $statement->execute(array(
                ':a_id' => $id,
            ));
            $result = $statement->fetch(PDO::FETCH_ASSOC);
            if ($result) {
                $result['review_score'] = $this->getReviewScore($id);
                $result['authors'] = $this->_get_authors($id);
                $new_review = $this->createNew($result);
                $new_review->set_id($id);
                return $new_review;
            } else {
                echo 'No review with that id (' . $id . ') found.';
            }
        } catch (PDOException $e) {
            $this->_database_connection->close_connection();
            echo 'ERROR: ' . $e->getMessage();
        }
    }

    private function getReviewScore($a_id)
    {
        $stmt = 'SELECT score FROM review_scores WHERE a_id=:review_id';
        $statement = $this->_database_connection->get_connection()->prepare($stmt);
        $statement->execute(array(
            'review_id' => $a_id,
        ));
        $user_mapper = new UserMapper($this->_database_connection);
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        return $result['score'];
    }

    public function getAll()
    {
        try
        {
            $this->_database_connection->connect();
            $stmt = 'SELECT * FROM articles WHERE type="review"';
            $statement = $this->_database_connection->get_connection()->prepare($stmt);
            $statement->execute();
            $reviews = array();
            while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
                $row['authors'] = $this->_get_authors($row['a_id']);
                $row['review_score'] = $this->getReviewScore($row['a_id']);
                $new_review = $this->createNew($row);
                $new_review->set_id($row['a_id']);
                $reviews[] = $new_review;
            }
            $this->_database_connection->close_connection();
            return $reviews;
        } catch (PDOException $e) {
            $this->_database_connection->close_connection();
            echo 'ERROR: ' . $e->getMessage();
        }
    }

    public function getRecent($limit)
    {
        try
        {
            $this->_database_connection->connect();
            $stmt = 'SELECT a_id FROM articles WHERE status="published" AND type="review" ORDER BY publish_date DESC LIMIT :lim';
            $statement = $this->_database_connection->get_connection()->prepare($stmt);
            $statement->bindParam(':lim', $limit, PDO::PARAM_INT);
            $statement->execute();
            $articles = array();
            while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
                $articles[] = $this->findById($row['a_id']);
            }
            return $articles;
        } catch (PDOException $e) {
            $this->_database_connection->close_connection();
            echo 'ERROR: ' . $e->getMessage();
        }
    }

    protected function _save_to_database(AbstractObject $obj)
    {
        $this->_database_connection->connect();
        try
        {
            $stmt = 'INSERT INTO articles (content, status, title, publish_date, type, cover_image) VALUES (:content, :status, :title, CURDATE(), :type, :cover_image)';
            $statement = $this->_database_connection->get_connection()->prepare($stmt);
            $statement->execute(array(
                ':content' => $obj->content,
                ':status' => $obj->status,
                ':title' => $obj->title,
                ':type' => $obj->type,
                ':cover_image' => $obj->cover_image,
            ));

            $this_review_id = $this->_database_connection->get_connection()->lastInsertID();
            $stmt = 'INSERT INTO authorship (username, a_id) VALUES (:username, :article_id)';
            $statement = $this->_database_connection->get_connection()->prepare($stmt);
            foreach ($obj->authors as $author) {
                $statement->execute(array(
                    ':username' => $author->get_id(),
                    ':article_id' => $this_review_id,
                ));
            }

            $stmt = 'INSERT INTO review_scores (a_id, score) VALUES (:article_id, :score)';
            $statement = $this->_database_connection->get_connection()->prepare($stmt);
            $statement->execute(array(
                ':article_id' => $this_review_id,
                ':score' => $obj->review_score,
            ));

            $this->_database_connection->close_connection();
            return $this_review_id;

        } catch (PDOException $e) {
            $this->_database_connection->close_connection();
            echo 'ERROR: ' . $e->getMessage();
        }
    }
}
