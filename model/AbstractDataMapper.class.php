<?php

abstract class AbstractDataMapper
{
    protected $_database_connection = null;

    public function __construct(DatabaseConnection $database_connection)
    {
        $this->_database_connection = $database_connection;
    }
    
    protected function _get_authors($a_id)
    {
        try
        {
            $this->_database_connection->connect();
            $auth_stmt = 'SELECT username FROM authorship WHERE a_id=:article_id';
            $auth_statement = $this->_database_connection->get_connection()->prepare($auth_stmt);
            $auth_statement->execute(array(
                'article_id' => $a_id
            ));
            $authors = array();
            $user_mapper = new UserMapper($this->_database_connection);
            while ($row = $auth_statement->fetch(PDO::FETCH_ASSOC))
            {
                $authors[] = $user_mapper->find_by_id($row['username']);
            }
            return $authors;
        } catch(PDOException $e) {
            $this->_database_connection->close_connection();
            echo 'ERROR: ' . $e->getMessage();
        }
    }

    abstract public function create_new(array $data);
    abstract public function save(AbstractObject $obj);
    abstract public function delete(AbstractObject $obj);
    abstract public function update(AbstractObject $obj);
    abstract public function get_all();
    abstract public function find_by_id($id);
}

?>