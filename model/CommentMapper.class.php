<?php

class CommentMapper extends AbstractDataMapper
{
    public function create_new(array $data)
    {
        if(!is_null($data))
        {
            $comment = new Comment();
            $comment->contents = $data['contents'];
            $comment->author = $data['author'];
            $comment->a_id = $data['a_id'];
            return $comment;
        } else {
            throw new Exception('Need data.');
        }
    }

    public function save(AbstractObject $obj)
    {
        $obj->set_id($this->_save_to_database($obj));
        $obj->time = $this->get_timestamp_by_id($obj->get_id());
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

    public function find_all_by_article_id($article_id)
    {
        try
        {
            $this->_database_connection->connect();
            $stmt = 'SELECT * FROM editor_comments WHERE a_id=:id';
            $statement = $this->_database_connection->get_connection()->prepare($stmt);
            $statement->execute(array(
                ':id' => $article_id
            ));
            $comments = array();
            while ($row = $statement->fetch(PDO::FETCH_ASSOC))
            {
                $comments[] = create_new($row);
            }
            $this->_database_connection->close_connection();
            return $comments;
        } catch(PDOException $e) {
            $this->_database_connection->close_connection();
            echo 'ERROR: ' . $e->getMessage();
        }
    }

    public function get_timestamp_by_id($id)
    {
        try
        {
            $stmt = 'SELECT time FROM editor_comments WHERE ec_id=:id';
            $statement = $this->_database_connection->get_connection()->prepare($stmt);
            $statement->execute(array(
                'ec_id' => $id
            ));
            $result = $statement->fetch(PDO::FETCH_ASSOC);
            return $result['time'];
            
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
            $stmt = 'INSERT INTO editor_comments (username, a_id, content) VALUES (:username, :a_id, content)';
            $statement = $this->_database_connection->get_connection()->prepare($stmt);
            $statement->execute(array(
                ':username' => $obj->username,
                ':a_id' => $obj->a_id,
                ':content' => $obj->content
            ));
            
            $this_comment_id = $this->_database_connection->get_connection()->lastInsertID();

            $this->_database_connection->close_connection();
            return $this_comment_id;
            
        } catch(PDOException $e) {
            $this->_database_connection->close_connection();
            echo 'ERROR: ' . $e->getMessage();
        }
    }
}

?>