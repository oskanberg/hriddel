<?php

abstract class AbstractDataMapper
{
    protected $_database_connection = null;

    public function __construct(DatabaseConnection $database_connection)
    {
        $this->_database_connection = $database_connection;
    }

    abstract public function create_new(array $data);
    abstract public function save(AbstractObject $obj);
    abstract public function delete(AbstractObject $obj);
    abstract public function update(AbstractObject $obj);
    abstract public function find_by_id($id);
    abstract protected function _save_to_database(AbstractObject $obj);
}

?>