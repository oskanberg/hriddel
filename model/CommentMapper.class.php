<?php

class CommentMapper extends AbstractDataMapper
{
    /**
    * Create a new comment article given some data
    * @param Array() $data an array of content, username, article id
    * @return Comment $comment new comment
    */
    public function create_new(array $data)
    {
        if(!is_null($data))
        {
            $comment = new Comment();
            $comment->content = $data['content'];
            $comment->username = $data['username'];
            $comment->a_id = $data['a_id'];
            return $comment;
        } else {
            throw new Exception('Need data.');
        }
    }


    /**
    * save a given column article to the database
    * @param ColumnArticle $obj an ColumnArticle object to save
    */
    public function save(AbstractObject $obj)
    {
        $obj->set_id($this->_save_to_database($obj));
        $obj->time = $this->get_timestamp_by_id($obj->get_id());
    }

   /**
    * the scope of this assessment doesn't need comments deleting
    */
    public function delete(AbstractObject $obj)
    {

    }

   /**
    * the scope of this assessment doesn't need comments editing
    */
    public function update(AbstractObject $obj)
    {

    }

   /**
    * the scope of this assessment doesn't require getting all comments
    */
    public function get_all()
    {
        
    }
    
   /**
    * the scope of this assessment doesn't require getting comments by id
    */
    public function find_by_id($id)
    {

    }

    /**
    * find all comments for a given article id
    * @param integer $a_id the id of the article
    * @return Comment $comments the comments belonging to the article
    */
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
                $comments[] = $this->create_new($row);
            }
            $this->_database_connection->close_connection();
            return $comments;
        } catch(PDOException $e) {
            $this->_database_connection->close_connection();
            echo 'ERROR: ' . $e->getMessage();
        }
    }


   /**
    * get the date of comment by its id
    * @param int $id the id of the comment
    * @return string the time it was posted
    */
    public function get_timestamp_by_id($id)
    {
        try
        {
            $stmt = 'SELECT time FROM editor_comments WHERE ec_id=:id';
            $statement = $this->_database_connection->get_connection()->prepare($stmt);
            $statement->execute(array(
                ':id' => $id
            ));
            $result = $statement->fetch(PDO::FETCH_ASSOC);
            return $result['time'];
            
        } catch(PDOException $e) {
            $this->_database_connection->close_connection();
            echo 'ERROR: ' . $e->getMessage();
        }
    }
    
   /**
    * save a new object to the database.
    * @access protected
    * @param Comment $obj the comment to save to the database 
    */
    protected function _save_to_database(AbstractObject $obj)
    {
        $this->_database_connection->connect();
        try
        {
            $stmt = 'INSERT INTO editor_comments (username, a_id, content) VALUES (:username, :a_id, :content)';
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