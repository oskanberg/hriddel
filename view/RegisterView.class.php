<?php

class RegisterView extends View
{
    private $template = 'register.tpl';
    private $title = 'Register';

    public function display()
    {
        $data = array(
            'title' => $this->title,
            'view_specific_template' => $this->template,
            'register_result_text' => '',
            'register_result' => false
        );
        if (isset($_POST['username']) && isset($_POST['name']))
        {
            $this->_controller->register_user($_POST['username'], $_POST['name']);
            $data['register_result'] = true;
            $data['register_result_text'] = '<p>Register successful. You are now logged in.</p>';
        }
        include_once(TEMPLATE_PATH . 'base_template.tpl');
    }
}

?>