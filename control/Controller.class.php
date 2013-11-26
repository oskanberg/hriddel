<?php

abstract class Controller
{
    protected $_model;

    public function __construct($model)
    {
        $this->_model = $model;
    }

}

?>