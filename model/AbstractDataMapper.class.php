<?php

abstract class AbstractDataMapper
{
    protected $database_connection = null;

    public function __construct($database_connection)
    {
        $this->database_connection = $database_connection;
    }

    abstract public function create_new(array $data);
    abstract public function delete(AbstractObject $obj);
    abstract public function update(AbstractObject $obj);
    abstract public function find_by_id($id);
    abstract protected function save_to_database(AbstractObject $obj);
}

?>