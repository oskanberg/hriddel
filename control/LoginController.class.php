<?php

class LoginController extends Controller
{
    public function login($username)
    {
        if ($this->_model->authenticate_username($username))
        {
            $_SESSION['u_id'] = $username;
        }
    }
}

?>