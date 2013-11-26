<?php

class UserMapper extends AbstractDataMapper
{
    public function create_new(array $data)
    {
        if(!is_null($data))
        {
            $new_user = new User();
            $new_user->username = $data['username'];
            $new_user->name = $data['name'];
            $new_user->type = $data['type'];
            $new_user->set_id($data['username']);
            return $new_user;
        } else {
            throw new Exception('Need data.');
        }
    }
    
    public function save(AbstractObject $obj)
    {
        $this->_save_to_database($obj);
    }

    public function delete(AbstractObject $obj)
    {

    }

    public function update(AbstractObject $obj)
    {

    }
    
    public function find_by_id($username)
    {
        $this->_database_connection->connect();
        try
        {
            $stmt = 'SELECT * FROM users WHERE username=:username';
            $statement = $this->_database_connection->get_connection()->prepare($stmt);
            $statement->execute(array(
                ':username' => $username
            ));
            if ($statement->rowCount() > 0)
            {
                $result = $statement->fetch(PDO::FETCH_ASSOC);
                $data = array(
                    'username' => $result['username'],
                    'name' => $result['name'],
                    'type' => $result['type']
                );
                return $this->create_new($data);
            } else {
                return null;
            }
            
        } catch(PDOException $e) {
            $this->_database_connection->close_connection();
            echo 'ERROR: ' . $e->getMessage();
        }
    }


    protected function _save_to_database(AbstractObject $obj)
    {
        $this->_database_connection->connect();
        try
        {
            $stmt = 'INSERT INTO users (username, name, type) VALUES (:username, :name, :type)';
            $statement = $this->database_connection->get_connection()->prepare($stmt);
            $statement->execute(array(
                ':username' => $obj->username,
                ':name' => $obj->name,
                ':type' => $obj->type
            ));
            $this->_database_connection->close_connection();
        } catch(PDOException $e) {
            $this->_database_connection->close_connection();
            echo 'ERROR: ' . $e->getMessage();
        }
    }
}

?>