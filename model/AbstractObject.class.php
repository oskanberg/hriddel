<?php

/**
* an abstract object for all domain objects to inherit
*/
abstract class AbstractObject
{
    /**
     * $_id private variable that contains the id of the object
     * @access private
     * @var integer|string
     */
    private $_id = null;
    
    /**
    * set the id of this object
    * @param integer|string $id the id of the object
     */
    public function set_id($id)
    {
        if (is_null($this->_id))
        {
            $this->_id = $id;
        } else {
            throw new Exception('Object id already set.');
        }
    }

    /**
    * get the id of this object
    * @return integer|string $id the id of the object
     */
    public function get_id()
    {
        return $this->_id;
    }
}

?>