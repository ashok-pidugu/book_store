<?php

class Database
{
    private $user;
    private $host;
    private $pass;
    private $db;
    public $connection;

    public function __construct()
    {
        $this->user = "root";
        $this->host = "localhost";
        $this->pass = "";
        $this->db = "book_store";
    }

    private function init()
    {
        try {
            $this->connection = new mysqli(
                $this->host,
                $this->user,
                $this->pass,
                $this->db
            );
        } catch (Exception $e) {
            die("Connection failed: " . $e->getMessage());
        }

    }
    public function connect()
    {
        $this->init();
        if ($this->connection->connect_error) {
            die("Connection failed: " . $this->connection->connect_error);
        }
        return $this->connection;
    }
}