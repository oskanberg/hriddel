<?php

class UserMapper extends AbstractDataMapper
{
    public function createNew(array $data)
    {
        if (!is_null($data)) {
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

    /*
     * Cannot update username
     */
    public function update(AbstractObject $obj)
    {
        if (is_null($this->findById($obj->username))) {
            throw new Exception('User to update not found');
        }
        $this->_database_connection->connect();
        $stmt = 'UPDATE users SET type=:type, name=:name WHERE username=:username';
        $statement = $this->_database_connection->get_connection()->prepare($stmt);
        $statement->execute(array(
            ':username' => $obj->username,
            ':type' => $obj->type,
            ':name' => $obj->name,
        ));
    }

    public function getAll()
    {
        $this->_database_connection->connect();
        $stmt = 'SELECT * FROM users';
        $statement = $this->_database_connection->get_connection()->prepare($stmt);
        $statement->execute();
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            // create array of user objects
            $users[] = $this->createNew($row);
        }
        $this->_database_connection->close_connection();
        return $users;
    }

    public function findById($username)
    {
        $this->_database_connection->connect();
        try
        {
            $stmt = 'SELECT * FROM users WHERE username=:username';
            $statement = $this->_database_connection->get_connection()->prepare($stmt);
            $statement->execute(array(
                ':username' => $username,
            ));
            if ($statement->rowCount() > 0) {
                $result = $statement->fetch(PDO::FETCH_ASSOC);
                $data = array(
                    'username' => $result['username'],
                    'name' => $result['name'],
                    'type' => $result['type'],
                );
                $this->_database_connection->close_connection();
                return $this->createNew($data);
            } else {
                $this->_database_connection->close_connection();
                return null;
            }

        } catch (PDOException $e) {
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
            $statement = $this->_database_connection->get_connection()->prepare($stmt);
            $statement->execute(array(
                ':username' => $obj->username,
                ':name' => $obj->name,
                ':type' => $obj->type,
            ));
            $this->_database_connection->close_connection();
        } catch (PDOException $e) {
            $this->_database_connection->close_connection();
            echo 'ERROR: ' . $e->getMessage();
        }
    }
}
