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
        if (!is_null($data['review_score']))
        {
            // it's a review we want
            return $this->review_mapper->create_new($data);
        } else if (!is_null($data['column_name'])) {
            // it's a column we want
            return $this->column_article_mapper->create_new($data);
        } else {
            // by process of elimitation
            return $this->article_mapper->create_new($data);
        }
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

    /*
    * assume we won't change any article type-specific attributes
    */
    public function update(AbstractObject $obj)
    {
        $this->get_mapper_for_object($obj)->update($obj);
    }
    
    public function find_by_id($a_id)
    {
        return $this->get_mapper_for_id($a_id)->find_by_id($a_id);
    }
    
    public function get_all()
    {
        $articles = array_merge(
            $this->column_article_mapper->get_all(),
            $this->review_mapper->get_all(),
            $this->article_mapper->get_all()
        );
        return $articles;
    }
}

?>