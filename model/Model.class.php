<?php

abstract class Model
{
    protected $_database_connection;

    public function __construct()
    {
        $host = 'localhost';
        $database_name = 'iapt';
        $username = 'root';
        $password = 'iaptassessment42';
        $this->_database_connection = new DatabaseConnection($host, $database_name, $username, $password);
    }

    public function is_user_logged_in()
    {
        if (isset($_SESSION['u_id']))
        {
            return true;
        } else {
            return false;
        }
    }

    public function get_logged_in_username()
    {
        if (isset($_SESSION['u_id']))
        {
            return $_SESSION['u_id'];
        } else {
            return null;
        }
    }
}

?>