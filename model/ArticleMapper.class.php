<?php

class ArticleMapper extends AbstractDataMapper
{
    public function create_new(array $data)
    {
        if(!is_null($data))
        {
            $new_article = new Article();
            $new_article->contents = $data['contents'];
            $new_article->authors = $data['authors'];
            $new_article->title = $data['title'];
            $new_article->type = $data['type'];
            $new_article->status = $data['status'];
            $new_article->cover_image = $data['cover_image'];
            return $new_article;
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

    /*
    * assume we won't change any article type-specific attributes
    */
    public function update(AbstractObject $obj)
    {
        echo $obj->status;
        try
        {
            $this->_database_connection->connect();
            $stmt = 'UPDATE articles SET contents=:contents, status=:status, title=:title, cover_image=:cover_image WHERE a_id=:a_id';
            $statement = $this->_database_connection->get_connection()->prepare($stmt);
            $statement->execute(array(
                ':contents' => $obj->contents,
                ':status' => $obj->status,
                ':title' => $obj->title,
                ':cover_image' => $obj->cover_image,
                ':a_id' => $obj->get_id()
            ));
            $this->_database_connection->close_connection();
        } catch(PDOException $e) {
            $this->_database_connection->close_connection();
            echo 'ERROR: ' . $e->getMessage();
        }
    }
    
    public function find_by_id($a_id)
    {
        $this->_database_connection->connect();
        try
        {
            $stmt = 'SELECT * FROM articles WHERE a_id=:a_id';
            $statement = $this->_database_connection->get_connection()->prepare($stmt);
            $statement->execute(array(
                ':a_id' => $a_id
            ));
            $result = $statement->fetch(PDO::FETCH_ASSOC);
            $this->_database_connection->close_connection();
            $result['authors'] = $this->_get_authors($a_id);
            $article = $this->create_new($result);
            $article->set_id($a_id);
            return $article;
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
            $stmt = 'SELECT * FROM articles WHERE type="article"';
            $statement = $this->_database_connection->get_connection()->prepare($stmt);
            $statement->execute();
            while ($row = $statement->fetch(PDO::FETCH_ASSOC))
            {
                $row['authors'] = $this->_get_authors($row['a_id']);
                $new_article = $this->create_new($row);
                $new_article->set_id($row['a_id']);
                $articles[] = $new_article;
            }
            $this->_database_connection->close_connection();
            return $articles;
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
            
            $this_article_id = $this->_database_connection->get_connection()->lastInsertID();
            $stmt = 'INSERT INTO authorship (username, a_id) VALUES (:username, :article_id)';
            $statement = $this->_database_connection->get_connection()->prepare($stmt);
            foreach ($obj->authors as $author)
            {
                $statement->execute(array(
                    ':username' => $author->get_id(),
                    ':article_id' => $this_article_id
                ));
            }
            $this->_database_connection->close_connection();
            return $this_article_id;
            
        } catch(PDOException $e) {
            $this->_database_connection->close_connection();
            echo 'ERROR: ' . $e->getMessage();
        }
    }
}

?>