<?php

Abstract class Controller
{
    protected $model;

    public function __construct($model)
    {
        $this->model = $model;
    }

}

?>