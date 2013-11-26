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
            'login_result_text' => ''
        );
        if (isset($_POST['u_id']))
        {
            $this->_controller->login($_POST['u_id']);
            $username = $this->_model->get_logged_in_username();
            if (!is_null($username))
            {
                $data['login_result'] = true;
                $data['login_result_text'] = 'Welcome ' . $username;
            } else {
                $data['login_result'] = false;
                $data['login_result_text'] = '<span style="color:red">User not found</span>';
            }
        }
        include_once(TEMPLATE_PATH . 'base_template.tpl');
    }

    public function logout()
    {
        $this->_controller->logout();
        $this->display();
    }
}

?>