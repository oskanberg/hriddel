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
            $new_user->set_id($this->save_to_database($new_user));
            return $new_user;
        } else {
            throw new Exception('Need data.');
        }
    }

    public function delete(AbstractObject $obj)
    {

    }

    public function update(AbstractObject $obj)
    {

    }
    
    public function find_by_id($id)
    {

    }


    protected function save_to_database(AbstractObject $obj)
    {
        $this->database_connection->connect();
        try
        {
            $stmt = 'INSERT INTO users (username, name, type) VALUES (:username, :name, :type)';
            $statement = $this->database_connection->get_connection()->prepare($stmt);
            $statement->execute(array(
                ':username' => $obj->username,
                ':name' => $obj->name,
                ':type' => $obj->type
            ));
            
            $this_user_id = $this->database_connection->get_connection()->lastInsertID();
            $this->database_connection->close_connection();
            
            return $this_user_id;
            
        } catch(PDOException $e) {
            $this->database_connection->close_connection();
            echo 'ERROR: ' . $e->getMessage();
        }
    }
}

?>