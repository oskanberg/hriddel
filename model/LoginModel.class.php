<?php

class LoginModel extends Model
{
    private $user_mapper;

    public function __construct()
    {
        Model::__construct();
        $this->user_mapper = new UserMapper($this->_database_connection);
    }

    public function authenticate_username($username)
    {
        $user = $this->user_mapper->find_by_id($username);
        if (is_null($user))
        {
            return false;
        } else {
            return true;
        }
    }
}

?>