<?php

/**
 * a class to encapsulate the ability to speak to the database
 */
class DatabaseConnection
{
    private $host;
    private $database_name;
    private $username;
    private $password;
    private $pdo_connection = null;

    /**
     * construct with database host, database name, username, password
     * @param string $host
     * @param string $database_name
     * @param string $username
     * @param string $password
     */
    public function __construct($host, $database_name, $username, $password)
    {
        $this->host = $host;
        $this->database_name = $database_name;
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * connect to the database
     * @return boolean whether connect succeeded
     */
    public function connect()
    {
        if (!is_null($this->pdo_connection)) {
            // might already have connected
            return true;
        }
        try
        {
            $conn_str = 'mysql:host=' . $this->host . ';dbname=' . $this->database_name;
            $this->pdo_connection = new PDO($conn_str, $this->username, $this->password);
            $this->pdo_connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo 'ERROR: ' . $e->getMessage();
            return false;
        }
        return true;
    }

    /**
     * get the active connection (make one if there isn't one already)
     * @return PDO the active connection
     */
    public function getConnection()
    {
        if (!is_null($this->pdo_connection)) {
            return $this->pdo_connection;
        } else {
            // try connecting, in case we forgot
            if ($this->connect()) {
                return $this->pdo_connection;
            } else {
                // uh oh
                echo "Can't get connection: won't connect (should have been an error)";
            }
        }
    }

    /**
     * destroy the current connection to the database
     */
    public function closeConnection()
    {
        $this->pdo_connection = null;
    }
}
