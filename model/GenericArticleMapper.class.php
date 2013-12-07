<?php

/*
* a class to handle the three types of article
* handles requests by passing them to the correct mappers
* adds features common to all articles on the way back
*/
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
    
    /**
    * create a new object of either review, article, column article
    * which to return is determined by what data is passed in.
    * add editors and whether or not it is highlighted as attributes
    * @return AbstractObject $article the new article
    */
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
        return $article;
    }

    /**
    * get the correct mapper for a given object, based on its class
    * @access private
    * @return AbstractDataMapper reference to the correct mapper to use
    */
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

    /**
    * get the correct mapper for a given id, based on the type that id references
    * @access private
    * @return AbstractDataMapper reference to the correct mapper to use
    */
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
    
    /**
    * save a given article to the database
    * @param AbstractObject $obj an article object to save
    */
    public function save(AbstractObject $obj)
    {
        $this->get_mapper_for_object($obj)->save($obj);
    }

    
   /**
    * the scope of this assessment doesn't need comments deleting
    */
    public function delete(AbstractObject $obj)
    {
    }

    /**
    * add another user as editor to this article
    * @param int $a_id the id of the article we want to add an editor to
    * @param User $editor the user we want to add as editor
    */
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

    /**
    * highlight an article
    * @param AbstractObject $article the article we want to highlight
    */
    public function highlight(AbstractObject $obj)
    {
        $this->_database_connection->connect();
        try
        {
            $stmt = 'INSERT INTO highlights (a_id) VALUES (:a_id)';
            $statement = $this->_database_connection->get_connection()->prepare($stmt);
            $statement->execute(array(
                ':a_id' => $obj->get_id(),
            ));
            $this->_database_connection->close_connection();
        } catch(PDOException $e) {
            $this->_database_connection->close_connection();
            echo 'ERROR: ' . $e->getMessage();
        }
    }

    /**
    * get max-limited array of highlighted articles, most recent first 
    * @param integer $limit the max number to return
    */
    public function get_recent_highlighted_articles($limit)
    {
        try
        {
            $this->_database_connection->connect();
            $stmt = 'SELECT a_id FROM highlights ORDER BY time DESC LIMIT :lim';
            $statement = $this->_database_connection->get_connection()->prepare($stmt);
            $statement->bindParam(':lim', $limit, PDO::PARAM_INT);
            $statement->execute();
            $articles = array();
            while ($row = $statement->fetch(PDO::FETCH_ASSOC))
            {
                $articles[] = $this->get_mapper_for_id($row['a_id'])->find_by_id($row['a_id']);
            }
            return $articles;
        } catch(PDOException $e) {
            $this->_database_connection->close_connection();
            echo 'ERROR: ' . $e->getMessage();
        }
    }

    /**
    * check whether a given article is highlighted
    * @param integer $a_id the id of the article in question
    */
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
            if ($result['count(*)'] > 0)
            {
                return true;
            } else {
                return false;
            }
        } catch(PDOException $e) {
            $this->_database_connection->close_connection();
            echo 'ERROR: ' . $e->getMessage();
        }
    }
    
   /**
    * update an article's corresponding database record
    * @param AbstractObject $obj an article object to update
    */
    public function update(AbstractObject $obj)
    {
        $this->get_mapper_for_object($obj)->update($obj);
    }
    
    /**
    * find an article by its id
    * @param integer $a_id the article id
    * @return AbstractObject instance (either article, column article, review)
    */
    public function find_by_id($a_id)
    {
        $article = $this->get_mapper_for_id($a_id)->find_by_id($a_id);
        $article->edtiors = $this->get_article_editors($article->get_id());
        $article->highlighted = $this->is_article_highlighted($article->get_id());
        return $article;
    }
    
    /**
    * get all articles
    * @return array(AbstractObject) merged array of all reviews, column articles, articles
    */
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
    
    /**
    * get all the editors of an article
    * @param integer $a_id the article id
    * @return array(User) all the editors of this piece
    */
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

    /**
    * register a like or dislike
    * @param User $user user to make the impression
    * @param integer $a_id article id
    * @param string $impression like/dislike
    */
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

    /**
    * get all the articles that a given user has given a given impression
    * e.g. get all the articles that Ed has disliked
    * @param User $user the user to check
    * @param string $impression to check for
    * @return array(AbstractObject) array of the articles
    */
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

    /**
    * get the articles with the most likes, up to a limit
    * @param integer $limit the max number to return
    * @return array(AbstractObject) array of the articles
    */
    public function get_most_liked($lim)
    {
        try
        {
            $this->_database_connection->connect();
            $stmt = 'SELECT a_id, COUNT(*) as c FROM likes_and_dislikes GROUP BY a_id ORDER BY c DESC';
            $statement = $this->_database_connection->get_connection()->prepare($stmt);
            $statement->bindParam(':lim', $limit, PDO::PARAM_INT);
            $statement->execute();
            $articles = array();
            while ($row = $statement->fetch(PDO::FETCH_ASSOC))
            {
                $articles[] = $this->get_mapper_for_id($row['a_id'])->find_by_id($row['a_id']);
            }
            return $articles;
        } catch(PDOException $e) {
            $this->_database_connection->close_connection();
            echo 'ERROR: ' . $e->getMessage();
        }
    }

    /**
    * update a record such that a given user is now listed as author
    * @param string $username username of the new author
    * @param integer $article_id the id of the article they are authoring
    */
    public function add_author($username, $article_id)
    {
        try
        {
            $this->_database_connection->connect();
            $this_article_id = $this->_database_connection->get_connection()->lastInsertID();
            $stmt = 'INSERT INTO authorship (username, a_id) VALUES (:username, :article_id)';
            $statement = $this->_database_connection->get_connection()->prepare($stmt);
            $statement->execute(array(
                ':username' => $username,
                ':article_id' => $article_id
            ));
            $this->_database_connection->close_connection();
        } catch(PDOException $e) {
                $this->_database_connection->close_connection();
                echo 'ERROR: ' . $e->getMessage();
        }
    }
}

?>