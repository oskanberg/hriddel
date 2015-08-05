<?php

abstract class Model
{
    protected $_database_connection;
    protected $_error = false;
    protected $_error_string = null;
    protected $_user_mapper;

    public function __construct()
    {
        $host = 'mysql-student';
        $database_name = 'Y6386774IAPT';
        $username = 'Y6386774';
        $password = '';
        $this->_database_connection = new DatabaseConnection($host, $database_name, $username, $password);
        $this->_user_mapper = new UserMapper($this->_database_connection);
    }

    /**
     * record that an error has occured in the current request
     * record a string to display back to the user
     * @param string $error_string the error to display
     */
    protected function _record_error($error_string)
    {
        $this->_error = true;
        $this->_error_string = $error_string;
    }

    /**
     * check whether an error exists
     * @return boolean whether there's an error
     */
    public function errorExists()
    {
        return $this->_error;
    }

    /**
     * get the error string
     * @return string error string
     */
    public function getErrorString()
    {
        if (!is_null($this->_error_string)) {
            return $this->_error_string;
        } else {
            return 'No error.'; // might change this to exception
        }
    }

    /**
     * is the current user logged in?
     * @return boolean
     */
    public function isUserLoggedIn()
    {
        if (isset($_SESSION['username'])) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * get the username of the current logged in user
     * @return string username
     */
    public function getLoggedInUsername()
    {
        if (isset($_SESSION['username'])) {
            return $_SESSION['username'];
        } else {
            return null;
        }
    }

    /**
     * get the User object of the current logged in user
     * @return User the current logged in user
     */
    public function getLoggedInUser()
    {
        if ($this->isUserLoggedIn()) {
            $user = $this->_user_mapper->find_by_id($_SESSION['username']);
            return $user;
        }
    }

    /**
     * get the type of the logged in user
     * @return string type of the logged in user
     */
    public function getLoggedInType()
    {
        return $this->getLoggedInUser()->type;
    }

    /**
     * check whether the current user is allowed to submit articles
     * @return boolean are they allowed?
     */
    public function canCurrentUserSubmitArticles()
    {
        if ($this->isUserLoggedIn()) {
            $user = $this->_user_mapper->find_by_id($_SESSION['username']);
            // the only type of user that can't submit articles is the subscriber
            if ($user->type == 'subscriber') {
                return false;
            } else {
                return true;
            }
        }
    }

    /**
     * check whether the current user is allowed to manage users
     * @return boolean are they allowed?
     */
    public function canCurrentUserManageUsers()
    {
        if ($this->isUserLoggedIn()) {
            $user = $this->_user_mapper->find_by_id($_SESSION['username']);
            if ($user->type == 'publisher') {
                return true;
            } else {
                return false;
            }
        }
    }
}
