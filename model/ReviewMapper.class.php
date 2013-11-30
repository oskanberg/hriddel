<?php

class ReviewMapper extends AbstractDataMapper
{
    public function create_new(array $data)
    {
        if(!is_null($data))
        {
            $new_review = new Review();
            $new_review->contents = $data['contents'];
            $new_review->authors = $data['authors'];
            $new_review->title = $data['title'];
            $new_review->type = $data['type'];
            $new_review->status = $data['status'];
            $new_review->cover_image = $data['cover_image'];
            $new_review->review_score = $data['review_score'];
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

    }
    
    public function find_by_id($id)
    {

    }

    private function get_review_score($a_id)
    {
        $stmt = 'SELECT score FROM review_scores WHERE a_id=:review_id';
        $statement = $this->_database_connection->get_connection()->prepare($stmt);
        $statement->execute(array(
            'review_id' => $a_id
        ));
        $authors = array();
        $user_mapper = new UserMapper($this->_database_connection);
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        return $result['score'];
    }
    
    private function get_authors($a_id)
    {
        try
        {
            $auth_stmt = 'SELECT username FROM authorship WHERE a_id=:review_id';
            $auth_statement = $this->_database_connection->get_connection()->prepare($auth_stmt);
            $auth_statement->execute(array(
                'review_id' => $a_id
            ));
            $authors = array();
            $user_mapper = new UserMapper($this->_database_connection);
            while ($row = $auth_statement->fetch(PDO::FETCH_ASSOC))
            {
                $authors[] = $user_mapper->find_by_id($row['username']);
            }
            return $authors;
        } catch(PDOException $e) {
            $this->_database_connection->close_connection();
            echo 'ERROR: ' . $e->getMessage();
        }
    }

    public function get_all()
    {
        try
        {
            $this->_database_connection->connect();
            $stmt = 'SELECT * FROM articles WHERE type="review"';
            $statement = $this->_database_connection->get_connection()->prepare($stmt);
            $statement->execute();
            while ($row = $statement->fetch(PDO::FETCH_ASSOC))
            {
                $row['authors'] = $this->get_authors($row['a_id']);
                $row['review_score'] = $this->get_review_score($row['a_id']);
                $new_review = $this->create_new($row);
                $new_review->set_id($row['a_id']);
                $reviews[] = $new_review;
            }
            $this->_database_connection->close_connection();
            return $reviews;
        } catch(PDOException $e) {
            $this->_database_connection->close_connection();
            echo 'ERROR: ' . $e->getMessage();
        }
    }
    
    protected function _save_to_database(AbstractObject $obj)
    {
        $this->_database_connection->connect();
        try
        {
            $stmt = 'INSERT INTO articles (contents, status, title, publish_date, type, cover_image) VALUES (:contents, :status, :title, CURDATE(), :type, :cover_image)';
            $statement = $this->_database_connection->get_connection()->prepare($stmt);
            $statement->execute(array(
                ':contents' => $obj->contents,
                ':status' => $obj->status,
                ':title' => $obj->title,
                ':type' => $obj->type,
                ':cover_image' => $obj->cover_image
            ));
            
            $this_review_id = $this->_database_connection->get_connection()->lastInsertID();
            $stmt = 'INSERT INTO authorship (username, a_id) VALUES (:username, :article_id)';
            $statement = $this->_database_connection->get_connection()->prepare($stmt);
            foreach ($obj->authors as $author)
            {
                $statement->execute(array(
                    ':username' => $author->get_id(),
                    ':article_id' => $this_review_id
                ));
            }

            $stmt = 'INSERT INTO review_scores (a_id, score) VALUES (:article_id, :score)';
            $statement = $this->_database_connection->get_connection()->prepare($stmt);
            $statement->execute(array(
                ':article_id' => $this_review_id,
                ':score' => $obj->review_score
            ));

            $this->_database_connection->close_connection();
            return $this_review_id;
            
        } catch(PDOException $e) {
            $this->_database_connection->close_connection();
            echo 'ERROR: ' . $e->getMessage();
        }
    }
}

?>