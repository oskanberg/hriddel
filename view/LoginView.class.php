<?php

class LoginView extends View
{
    private $template = 'login.tpl';
    private $title = 'Login';

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