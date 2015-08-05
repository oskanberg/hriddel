<?php

abstract class View
{
    protected $_controller;
    protected $_model;

    public function __construct($controller, $model)
    {
        $this->_controller = $controller;
        $this->_model = $model;
    }

    abstract public function display();
}
