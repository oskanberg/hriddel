<?php

class IndexView extends View
{
    private $template = 'index.tpl';
    private $title = 'Home';
    
    public function display()
    {
        $data = array(
            'title' => $this->title,
            'view_specific_template' => $this->template
        );
        include_once(TEMPLATE_PATH . 'base_template.tpl');
    }
}

?>