<?php

class UserManagementModel extends Model
{

    public function authenticate_username($username)
    {
        $user = $this->_user_mapper->find_by_id($username);
        if (!is_null($user))
        {
            $_SESSION['username'] = $username;
        } else {
            $this->_record_error('User ' . $username . ' not found');
        }
    }

    public function register_user($username, $name)
    {
        // before we do anything, check for uniqueness
        $possible_duplicate = $this->_user_mapper->find_by_id($username);
        if (!is_null($possible_duplicate))
        {
            $this->_record_error('Username ' . $username . ' has already been taken. Please choose another.');
        } else {
            // create the data for our new subscriber
            $data = array(
                'username' => $username,
                'name' => $name,
                'type' => 'subscriber'
            );
            // create new object
            $new_user = $this->_user_mapper->create_new($data);
            // save to the database
            $this->_user_mapper->save($new_user);
            $this->authenticate_username($username);
        }
    }

    public function get_users_array()
    {
        return $this->_user_mapper->get_all();
    }
}

?>