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
            'show_form' => true,
            'show_error' => false,
            'show_result' => false
        );
        if ($this->_model->is_user_logged_in())
        {
            $data['show_result'] = true;
            $data['register_result_text'] = 'Registration successful. You have been logged in.';
            $data['show_form'] = false;
        } else {
            if ($this->_model->error_exists())
            {
                $data['show_error'] = true;
                $data['error_string'] = '<p class="error">' . $this->_model->get_error_string() . '</p>';
            }
        }
        include_once(TEMPLATE_PATH . 'base_template.tpl');
    }
}

?>