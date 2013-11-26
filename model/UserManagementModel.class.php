<?php

class UserManagementModel extends Model
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

    public function register_user($username, $name)
    {
        // create the data for our new subscriber
        $data = array(
            'username' => $username,
            'name' => $name,
            'type' => 'subscriber'
        );
        // create new object
        $new_user = $this->user_mapper->create_new($data);
        // save to the database
        $this->user_mapper->save($new_user);
    }
}

?>