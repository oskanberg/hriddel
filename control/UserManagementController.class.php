<?php

class UserManagementController extends Controller
{
    public function login($username)
    {
        if ($this->_model->authenticate_username($username))
        {
            $_SESSION['u_id'] = $username;
        }
    }

    public function logout()
    {
        session_destroy();
        unset($_SESSION['u_id']);
    }

    public function register_user($username, $name)
    {
        $this->_model->register_user($username, $name);
        $this->login($username);
    }
}

?>