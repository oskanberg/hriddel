<?php

class ErrorView extends View
{    
    public function display()
    {
        if ($this->_model->error_exists())
        {
            echo $this->_model->get_error_string();
        }
    }
}

?>