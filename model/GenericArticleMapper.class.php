<?php

class GenericArticleMapper extends AbstractDataMapper
{

    private $article_mapper;
    private $column_article_mapper;
    private $review_mapper;
    
    public function __construct($database_connection)
    {
        parent::__construct($database_connection);
        $this->article_mapper = new ArticleMapper($this->_database_connection);
        $this->column_article_mapper = new ColumnArticleMapper($this->_database_connection);
        $this->review_mapper = new ReviewMapper($this->_database_connection);
    }
    
    public function create_new(array $data)
    {
        $article = null;
        if (!is_null($data['review_score']))
        {
            // it's a review we want
            $article = $this->review_mapper->create_new($data);
        } else if (!is_null($data['column_name'])) {
            // it's a column we want
            $article = $this->column_article_mapper->create_new($data);
        } else {
            // by process of elimitation
            $article = $this->article_mapper->create_new($data);
        }
        $article->editors = $this->get_article_editors($article->get_id());
        $article->highlighted = $this->is_article_highlighted($article->get_id());
        return article;
    }

    private function get_mapper_for_object($obj)
    {
        if ($obj instanceof Review)
        {
            return $this->review_mapper;
        } else if ($obj instanceof ColumnArticle) {
            return $this->column_article_mapper;
        } else {
            return $this->article_mapper;
        }
    }

    private function get_mapper_for_id($a_id)
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
            if ($result['type'] == 'article')
            {
                return $this->article_mapper;
            } else if ($result['type'] == 'column article') {
                return $this->column_article_mapper;
            } else {
                return $this->review_mapper;
            }
        } catch(PDOException $e) {
            $this->_database_connection->close_connection();
            echo 'ERROR: ' . $e->getMessage();
        }
    }

    public function save(AbstractObject $obj)
    {
        $this->get_mapper_for_object($obj)->save($obj);
    }


    public function delete(AbstractObject $obj)
    {
        $this->get_mapper_for_object($obj)->save($obj);
    }

    public function add_editor($a_id, $editor)
    {
        $this->_database_connection->connect();
        try
        {
            $stmt = 'INSERT INTO editor_map (username, a_id) VALUES (:username, :a_id)';
            $statement = $this->_database_connection->get_connection()->prepare($stmt);
            $statement->execute(array(
                ':username' => $editor->username,
                ':a_id' => $a_id
            ));
            $this->_database_connection->close_connection();
        } catch(PDOException $e) {
            $this->_database_connection->close_connection();
            echo 'ERROR: ' . $e->getMessage();
        }
    }

    public function highlight(AbstractObject $obj)
    {
        $this->_database_connection->connect();
        try
        {
            $stmt = 'INSERT INTO highlights (a_id) VALUES (:a_id)';
            $statement = $this->_database_connection->get_connection()->prepare($stmt);
            $statement->execute(array(
                ':a_id' => $obj->get_id(a_id),
            ));
            $this->_database_connection->close_connection();
        } catch(PDOException $e) {
            $this->_database_connection->close_connection();
            echo 'ERROR: ' . $e->getMessage();
        }
    }

    public function get_recent_highlighted_articles($limit)
    {
        try
        {
            $this->_database_connection->connect();
            $auth_stmt = 'SELECT a_id FROM highlights ORDER BY timestamp ASC';
            $auth_statement = $this->_database_connection->get_connection()->prepare($auth_stmt);
            $auth_statement->execute();
            $articles = array();
            $i = 0;
            while ($row = $auth_statement->fetch(PDO::FETCH_ASSOC) && $i < $limit)
            {
                $articles[] = $this->get_mapper_for_id($row['a_id'])->find_by_id($row['a_id']);
            }
            return $articles;
        } catch(PDOException $e) {
            $this->_database_connection->close_connection();
            echo 'ERROR: ' . $e->getMessage();
        }
    }

    public function is_article_highlighted($a_id)
    {
        try
        {
            $stmt = 'SELECT count(*) FROM highlights where a_id=:a_id';
            $statement = $this->_database_connection->get_connection()->prepare($stmt);
            $statement->execute(array(
                ':a_id' => $a_id
            ));
            $result = $statement->fetch(PDO::FETCH_ASSOC);
            if ($result > 0) {
                return true;
            } else {
                return false;
            }
        } catch(PDOException $e) {
            $this->_database_connection->close_connection();
            echo 'ERROR: ' . $e->getMessage();
        }
    }
    
    public function update(AbstractObject $obj)
    {
        $this->get_mapper_for_object($obj)->update($obj);
    }
    
    public function find_by_id($a_id)
    {
        $article = $this->get_mapper_for_id($a_id)->find_by_id($a_id);
        $article->edtiors = $this->get_article_editors($article->get_id());
        $article->highlighted = $this->is_article_highlighted($article->get_id());
        return $article;
    }
    
    public function get_all()
    {
        $articles = array_merge(
            $this->column_article_mapper->get_all(),
            $this->review_mapper->get_all(),
            $this->article_mapper->get_all()
        );
        foreach ($articles as $article)
        {
            $article->edtiors = $this->get_article_editors($article->get_id());
            $article->highlighted = $this->is_article_highlighted($article->get_id());
        }
        return $articles;
    }

    public function get_article_editors($a_id)
    {
        try
        {
            $this->_database_connection->connect();
            $stmt = 'SELECT DISTINCT(username) FROM editor_map WHERE a_id=:article_id';
            $statement = $this->_database_connection->get_connection()->prepare($stmt);
            $statement->execute(array(
                ':article_id' => $a_id
            ));
            $editors = array();
            $user_mapper = new UserMapper($this->_database_connection);
            while ($row = $statement->fetch(PDO::FETCH_ASSOC))
            {
                $editors[] = $user_mapper->find_by_id($row['username']);
            }
            return $editors;
        } catch(PDOException $e) {
            $this->_database_connection->close_connection();
            echo 'ERROR: ' . $e->getMessage();
        }
    }

    public function store_impression($user, $a_id, $impression)
    {
        try
        {
            $this->_database_connection->connect();
            // likes/dislikes assumed to be mutually exclusive.
            $stmt = 'DELETE FROM likes_and_dislikes WHERE a_id=:article_id AND username=:username';
            $statement = $this->_database_connection->get_connection()->prepare($stmt);
            $statement->execute(array(
                ':article_id' => $a_id,
                ':username' => $user->username
            ));

            $stmt = 'INSERT INTO likes_and_dislikes (username, a_id, impression) VALUES (:username, :article_id, :impression)';
            $statement = $this->_database_connection->get_connection()->prepare($stmt);
            $statement->execute(array(
                ':article_id' => $a_id,
                ':username' => $user->username,
                ':impression' => $impression
            ));
        } catch(PDOException $e) {
            $this->_database_connection->close_connection();
            echo 'ERROR: ' . $e->getMessage();
        }
    }

    public function get_all_articles_with_impression_by_user($user, $impression)
    {
        try
        {
            $this->_database_connection->connect();
            // likes/dislikes assumed to be mutually exclusive.
            $stmt = 'SELECT * FROM likes_and_dislikes WHERE username=:username AND impression=:impression';
            $statement = $this->_database_connection->get_connection()->prepare($stmt);
            $statement->execute(array(
                ':username' => $user->username,
                ':impression' => $impression
            ));
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
}

?>