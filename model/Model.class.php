<?php

abstract class Model
{
    protected $_database_connection;
    protected $_error = false;
    protected $_error_string = null;

    public function __construct()
    {
        $host = 'localhost';
        $database_name = 'iapt';
        $username = 'root';
        $password = 'iaptassessment42';
        $this->_database_connection = new DatabaseConnection($host, $database_name, $username, $password);
    }

    protected function _record_error($error_string)
    {
        $this->_error = true;
        $this->_error_string = $error_string;
    }

    public function error_exists()
    {
        return !is_null($this->_error);
    }
    
    public function get_error_string()
    {
        if (!is_null($this->_error_string))
        {
            return $this->_error_string;
        } else {
            return ''; // might change this to exception
        }
    }
    
    public function is_user_logged_in()
    {
        if (isset($_SESSION['username']))
        {
            return true;
        } else {
            return false;
        }
    }

    public function get_logged_in_username()
    {
        if (isset($_SESSION['username']))
        {
            return $_SESSION['username'];
        } else {
            return null;
        }
    }
}

?>