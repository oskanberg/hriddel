<?php

abstract class View
{
    protected $controller;

    public function __construct($controller)
    {
        $this->controller = $controller;
    }

    abstract public function display();
}

?>