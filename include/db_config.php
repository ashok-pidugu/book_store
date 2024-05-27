<?php
require_once dirname(__FILE__) . "/database.php";
$database = new Database();
$db_connection = $database->connect();