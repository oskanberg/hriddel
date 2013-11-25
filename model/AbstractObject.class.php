<?php

abstract class AbstractObject
{
    private $_id = null;

    public function set_id($id)
    {
        if (is_null($this->_id))
        {
            $this->_id = $id;
        } else {
            throw new Exception('Object id already set.');
        }
    }

    public function get_id()
    {
        return $this->_id;
    }
}

?>