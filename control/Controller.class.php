<?php

/**
 * Abstract class for controllers to inherit
 */
abstract class Controller
{
    /**
     * every controller has its corresponding model
     * @access protected
     * @var Model
     */
    protected $_model;

    /**
     * Construct the controller
     * @param Model $model
     */
    public function __construct($model)
    {
        $this->_model = $model;
    }

}
