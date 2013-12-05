<?php

abstract class Model
{
    protected $_database_connection;
    protected $_error = false;
    protected $_error_string = null;
    protected $_user_mapper;

    public function __construct()
    {
        $host = 'localhost';
        $database_name = 'iapt_assessment';
        $username = 'root';
        $password = 'iaptassessment42';
        $this->_database_connection = new DatabaseConnection($host, $database_name, $username, $password);
        $this->_user_mapper = new UserMapper($this->_database_connection);
    }

    protected function _record_error($error_string)
    {
        $this->_error = true;
        $this->_error_string = $error_string;
    }

    public function error_exists()
    {
        return $this->_error;
    }
    
    public function get_error_string()
    {
        if (!is_null($this->_error_string))
        {
            return $this->_error_string;
        } else {
            return 'No error.'; // might change this to exception
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

    public function get_logged_in_user()
    {
        if ($this->is_user_logged_in())
        {
            $user = $this->_user_mapper->find_by_id($_SESSION['username']);
            return $user;
        }
    }

    public function get_logged_in_type()
    {
        return $this->get_logged_in_user()->type;
    }
    
    public function can_current_user_submit_articles()
    {
        if ($this->is_user_logged_in())
        {
            $user = $this->_user_mapper->find_by_id($_SESSION['username']);
            // the only type of user that can't submit articles is the subscriber
            if ($user->type == 'subscriber')
            {
                return false;
            } else {
                return true;
            }
        }
    }

    public function can_current_user_manage_users()
    {
        if ($this->is_user_logged_in())
        {
            $user = $this->_user_mapper->find_by_id($_SESSION['username']);
            if ($user->type == 'editor' || $user->type == 'publisher')
            {
                return true;
            } else {
                return false;
            }
        }
    }
}

?>