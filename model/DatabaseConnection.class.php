<?php 

class DatabaseConnection
{
    private $host;
    private $database_name;
    private $username;
    private $password;
    private $pdo_connection = null;

    public function __construct($host, $database_name, $username, $password)
    {
        $this->host = $host;
        $this->database_name = $database_name;
        $this->username = $username;
        $this->password = $password;
    }

    public function connect()
    {
        if (!is_null($this->pdo_connection))
        {
            // might already have connected
            return true;
        }
        try
        {
            $conn_str = 'mysql:host=' . $this->host . ';dbname=' . $this->database_name;
            $this->pdo_connection = new PDO($conn_str, $this->username, $this->password);
            $this->pdo_connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            echo 'ERROR: ' . $e->getMessage();
            return false;
        }
        return true;
    }

    public function get_connection()
    {
        if(!is_null($this->pdo_connection))
        {
            return $this->pdo_connection;
        } else {
            // try connecting, in case we forgot
            if ($this->connect())
            {
                return $this->pdo_connection;
            } else {
                // uh oh
                echo "Can't get connection: won't connect (should have been an error)";
            }
        }
    }

    public function close_connection()
    {
        $this->pdo_connection = null;
    }
}

?>