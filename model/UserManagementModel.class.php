<?php

class UserManagementModel extends Model
{

    /**
     * try to log in with username, register error otherwise
     * @param string username
     */
    public function authenticateUsername($username)
    {
        $user = $this->_user_mapper->find_by_id($username);
        if (!is_null($user)) {
            $_SESSION['username'] = $username;
        } else {
            $this->_record_error('User ' . $username . ' not found');
        }
    }

    /**
     * register a user
     * @param string username of the new guy
     * @param string full name of the new guy
     */
    public function registerUser($username, $name)
    {
        // before we do anything, check for uniqueness
        $possible_duplicate = $this->_user_mapper->find_by_id($username);
        if (!is_null($possible_duplicate)) {
            $this->_record_error('Username ' . $username . ' has already been taken. Please choose another.');
        } else {
            // create the data for our new subscriber
            $data = array(
                'username' => $username,
                'name' => $name,
                'type' => 'subscriber',
            );
            // create new object
            $new_user = $this->_user_mapper->create_new($data);
            // save to the database
            $this->_user_mapper->save($new_user);
            $this->authenticateUsername($username);
        }
    }

    /**
     * get an array of all users
     * @return array(User) all users
     */
    public function getUsersArray()
    {
        return $this->_user_mapper->get_all();
    }

    /**
     * change the type of multiple users at once
     * @param array(User) the the users to change
     * @param string type to change them to
     */
    public function changeTypeMultiple(array $users, $type)
    {
        foreach ($users as $username) {
            $user = $this->_user_mapper->find_by_id($username);
            $user->type = $type;
            $this->_user_mapper->update($user);
        }
    }
}
