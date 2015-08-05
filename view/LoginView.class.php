<?php

class LoginView extends View
{
    private $template = 'login.tpl';
    private $title = 'Login';

    public function display()
    {
        $data = array(
            'title' => $this->title,
            'view_specific_template' => $this->template,
            'show_error' => false,
            'show_login' => true,
            'show_result_text' => false,
        );
        if ($this->_model->is_user_logged_in()) {
            $data['show_login'] = false;
            $data['show_result_text'] = true;
            $data['login_result_text'] = '<p>Welcome ' . $this->_model->get_logged_in_username() . '</p>';
        } else {
            if ($this->_model->error_exists()) {
                $data['show_error'] = true;
                $data['error_string'] = '<p class="error">' . $this->_model->get_error_string() . '</p>';
            }
        }
        include_once TEMPLATE_PATH . 'base_template.tpl';
    }
}
