<?php

class ColumnArticleMapper extends AbstractDataMapper
{
    public function create_new(array $data)
    {
        if(!is_null($data))
        {
            $new_column_article = new ColumnArticle();
            $new_column_article->contents = $data['contents'];
            $new_column_article->authors = $data['authors'];
            $new_column_article->title = $data['title'];
            $new_column_article->type = $data['type'];
            $new_column_article->status = $data['status'];
            $new_column_article->cover_image = $data['cover_image'];
            $new_column_article->column_name = $data['column_name'];
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

    }
    
    public function find_by_id($id)
    {

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