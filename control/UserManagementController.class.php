<?php

class UserManagementController extends Controller
{
    public function authenticate()
    {
        if (isset($_POST['username']))
        {
            $this->_model->authenticate_username($_POST['username']);
        }
    }

    public function logout()
    {
        session_destroy();
        unset($_SESSION['username']);
    }

    public function register_user()
    {
        if (isset($_POST['username']) && isset($_POST['name']))
        {
            $this->_model->register_user($_POST['username'], $_POST['name']);
        }
    }
    
    public function change_type_multiple()
    {
        if (isset($_POST['users']) && isset($_POST['type']))
        {
            $this->_model->change_type_multiple($_POST['users'], $_POST['type']);
        }
    }
}

?>