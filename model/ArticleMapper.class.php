<?php

class ArticleMapper extends AbstractDataMapper
{

    /**
     * Create a new article given some data
     * @param Array() $data an array of all the required data to make a new article
     * @return Article $new_article the newly created article object
     */
    public function createNew(array $data)
    {
        if (!is_null($data)) {
            $new_article = new Article();
            $new_article->content = $data['content'];
            $new_article->authors = $data['authors'];
            $new_article->title = $data['title'];
            $new_article->type = $data['type'];
            $new_article->status = $data['status'];
            $new_article->cover_image = $data['cover_image'];
            if (isset($data['publish_date'])) {
                $new_article->date = $data['publish_date'];
            }
            return $new_article;
        } else {
            throw new Exception('Need data.');
        }
    }

    /**
     * save a given article to the database
     * @param Article $obj an Article object to save
     */
    public function save(AbstractObject $obj)
    {
        $obj->set_id($this->_save_to_database($obj));
    }

    public function delete(AbstractObject $obj)
    {

    }

    /**
     * update a given article object's corresponding database
     * assume we will never want to change an article's type
     * @param Article $obj an Article object to update
     */
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
            $this->_database_connection->close_connection();
        } catch (PDOException $e) {
            $this->_database_connection->close_connection();
            echo 'ERROR: ' . $e->getMessage();
        }
    }

    /**
     * find an article by its id.
     * @param integer $a_id the id of the article
     * @return Article $article the article object
     */
    public function findById($a_id)
    {
        $this->_database_connection->connect();
        try
        {
            $stmt = 'SELECT * FROM articles WHERE a_id=:a_id';
            $statement = $this->_database_connection->get_connection()->prepare($stmt);
            $statement->execute(array(
                ':a_id' => $a_id,
            ));
            $result = $statement->fetch(PDO::FETCH_ASSOC);
            $this->_database_connection->close_connection();
            $result['authors'] = $this->_get_authors($a_id);
            $article = $this->createNew($result);
            $article->set_id($a_id);
            return $article;
        } catch (PDOException $e) {
            $this->_database_connection->close_connection();
            echo 'ERROR: ' . $e->getMessage();
        }
    }

    /**
     * get every single article
     * @return array(Article) $articles the array of all articles
     */
    public function getAll()
    {
        try
        {
            $this->_database_connection->connect();
            $stmt = 'SELECT * FROM articles WHERE type="article"';
            $statement = $this->_database_connection->get_connection()->prepare($stmt);
            $statement->execute();
            $articles = array();
            while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
                $row['authors'] = $this->_get_authors($row['a_id']);
                $new_article = $this->createNew($row);
                $new_article->set_id($row['a_id']);
                $articles[] = $new_article;
            }
            $this->_database_connection->close_connection();
            return $articles;
        } catch (PDOException $e) {
            $this->_database_connection->close_connection();
            echo 'ERROR: ' . $e->getMessage();
        }
    }

    /**
     * get a max-limited array of most recent articles
     * @param int the limit
     * @return array(Article) $articles the array of recent articles
     */
    public function getRecent($limit)
    {
        try
        {
            $this->_database_connection->connect();
            $stmt = 'SELECT a_id FROM articles WHERE status="published" AND type="article" ORDER BY publish_date DESC LIMIT :lim';
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

    /**
     * save a new object to the database.
     * @access protected
     * @param Article $obj the article to save to the database
     */
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

            $this_article_id = $this->_database_connection->get_connection()->lastInsertID();
            $stmt = 'INSERT INTO authorship (username, a_id) VALUES (:username, :article_id)';
            $statement = $this->_database_connection->get_connection()->prepare($stmt);
            foreach ($obj->authors as $author) {
                $statement->execute(array(
                    ':username' => $author->get_id(),
                    ':article_id' => $this_article_id,
                ));
            }
            $this->_database_connection->close_connection();
            return $this_article_id;

        } catch (PDOException $e) {
            $this->_database_connection->close_connection();
            echo 'ERROR: ' . $e->getMessage();
        }
    }
}
