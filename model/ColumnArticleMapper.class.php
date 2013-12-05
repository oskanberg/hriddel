<?php

class ColumnArticleMapper extends AbstractDataMapper
{
    public function create_new(array $data)
    {
        if(!is_null($data))
        {
            $new_column_article = new ColumnArticle();
            $new_column_article->content = $data['content'];
            $new_column_article->authors = $data['authors'];
            $new_column_article->title = $data['title'];
            $new_column_article->type = $data['type'];
            $new_column_article->status = $data['status'];
            $new_column_article->cover_image = $data['cover_image'];
            $new_column_article->column_name = $data['column_name'];
            if (isset($data['publish_date']))
            {
                $new_article->date = $data['publish_date'];
            }
            return $new_column_article;
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
                ':a_id' => $obj->get_id()
            ));

            $stmt = 'UPDATE column_mappings SET c_name=:c_name WHERE a_id=:a_id';
            $statement = $this->_database_connection->get_connection()->prepare($stmt);
            $statement->execute(array(
                ':c_name' => $obj->column_name,
                ':a_id' => $obj->get_id()
            ));
            $this->_database_connection->close_connection();
        } catch(PDOException $e) {
            $this->_database_connection->close_connection();
            echo 'ERROR: ' . $e->getMessage();
        }
    }
    
    public function find_by_id($id)
    {
        $this->_database_connection->connect();
        try
        {
            $stmt = 'SELECT * FROM articles WHERE a_id=:a_id';
            $statement = $this->_database_connection->get_connection()->prepare($stmt);
            $statement->execute(array(
                ':a_id' => $id
            ));
            $result = $statement->fetch(PDO::FETCH_ASSOC);
            if ($result)
            {
                $result['column_name'] = $this->get_column_name($id);
                $result['authors'] = $this->_get_authors($id);
                $new_column_article = $this->create_new($result);
                $new_column_article->set_id($id);
                return $new_column_article;
            } else {
                echo 'No review with that id (' . $id . ') found.';
            }
        } catch(PDOException $e) {
            $this->_database_connection->close_connection();
            echo 'ERROR: ' . $e->getMessage();
        }
    }

    private function get_column_name($a_id)
    {
        $this->_database_connection->connect();
        $stmt = 'SELECT c_name FROM column_mappings WHERE a_id=:article_id';
        $statement = $this->_database_connection->get_connection()->prepare($stmt);
        $statement->execute(array(
            'article_id' => $a_id
        ));
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        $this->_database_connection->close_connection();
        return $result['c_name'];
    }
    
    public function get_all()
    {
        $this->_database_connection->connect();
        $stmt = 'SELECT * FROM articles WHERE type="column article"';
        $statement = $this->_database_connection->get_connection()->prepare($stmt);
        $statement->execute();
        // initialise empty array in case there are none
        $column_articles = array();
        while ($row = $statement->fetch(PDO::FETCH_ASSOC))
        {
            $row['column_name'] = $this->get_column_name($row['a_id']);
            $row['authors'] = $this->_get_authors($row['a_id']);
            $new_column_article = $this->create_new($row);
            $new_column_article->set_id($row['a_id']);
            $column_articles[] = $new_column_article;
        }
        $this->_database_connection->close_connection();
        return $column_articles; 
    }

    public function get_recent($limit)
    {
        try
        {
            $this->_database_connection->connect();
            $stmt = 'SELECT a_id FROM articles WHERE status="published" AND type="column article" ORDER BY publish_date DESC LIMIT :lim';
            $statement = $this->_database_connection->get_connection()->prepare($stmt);
            $statement->bindParam(':lim', $limit, PDO::PARAM_INT);
            $statement->execute();
            $articles = array();
            while ($row = $statement->fetch(PDO::FETCH_ASSOC))
            {
                $articles[] = $this->find_by_id($row['a_id']);
            }
            return $articles;
        } catch(PDOException $e) {
            $this->_database_connection->close_connection();
            echo 'ERROR: ' . $e->getMessage();
        }
    }

    protected function _save_to_database(AbstractObject $obj)
    {
        try
        {
            $this->_database_connection->connect();
            $stmt = 'INSERT INTO articles (content, status, title, publish_date, type, cover_image) VALUES (:content, :status, :title, CURDATE(), :type, :cover_image)';
            $statement = $this->_database_connection->get_connection()->prepare($stmt);
            $statement->execute(array(
                ':content' => $obj->content,
                ':status' => $obj->status,
                ':title' => $obj->title,
                ':type' => $obj->type,
                ':cover_image' => $obj->cover_image
            ));
            
            $this_column_article_id = $this->_database_connection->get_connection()->lastInsertID();
            $stmt = 'INSERT INTO authorship (username, a_id) VALUES (:username, :article_id)';
            $statement = $this->_database_connection->get_connection()->prepare($stmt);
            foreach ($obj->authors as $author)
            {
                $statement->execute(array(
                    ':username' => $author->get_id(),
                    ':article_id' => $this_column_article_id
                ));
            }

            $stmt = 'INSERT INTO column_mappings (c_name, a_id) VALUES (:column_name, :article_id)';
            $statement = $this->_database_connection->get_connection()->prepare($stmt);
            $statement->execute(array(
                ':column_name' => $obj->column_name,
                ':article_id' => $this_column_article_id
            ));

            $this->_database_connection->close_connection();
            return $this_column_article_id;
            
        } catch(PDOException $e) {
            $this->_database_connection->close_connection();
            echo 'ERROR: ' . $e->getMessage();
        }
    }
}

?>