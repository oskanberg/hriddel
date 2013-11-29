<?php

class ErrorView extends View
{    
    public function display()
    {
        if ($this->_model->error_exists())
        {
            json_encode($this->_model->get_error_string());
        }
    }
}

?>