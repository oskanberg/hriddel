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
}

?>