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
            $new_article->set_id($this->save_to_database($new_article));
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

    public function update(AbstractObject $obj)
    {

    }
    
    public function find_by_id($id)
    {

    }

    protected function _save_to_database(AbstractObject $obj)
    {
        $this->database_connection->connect();
        try
        {
            $stmt = 'INSERT INTO articles (contents, status, title, publish_date, type, cover_image) VALUES (:contents, :status, :title, CURDATE(), :type, :cover_image)';
            $statement = $this->database_connection->get_connection()->prepare($stmt);
            $statement->execute(array(
                ':contents' => $obj->contents,
                ':status' => $obj->status,
                ':title' => $obj->title,
                ':type' => $obj->type,
                ':cover_image' => $obj->cover_image
            ));
            
            $this_article_id = $this->database_connection->get_connection()->lastInsertID();
            $stmt = 'INSERT INTO authorship (u_id, a_id) VALUES (:user_id, :article_id)';
            $statement = $this->database_connection->get_connection()->prepare($stmt);
            foreach ($obj->authors as $author)
            {
                $statement->execute(array(
                    ':user_id' => $author->get_id(),
                    ':article_id' => $this_article_id
                ));
            }
            $this->database_connection->close_connection();
            return $this_article_id;
            
        } catch(PDOException $e) {
            $this->database_connection->close_connection();
            echo 'ERROR: ' . $e->getMessage();
        }
    }
}

?>