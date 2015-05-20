<?php

class Connector {

    public $conn = null;

    function Connect(){
        $serverName = "ENTER SERVER NAME HERE";
        $dbname = "ENTER DATABASE NAME HERE";
        $userName = "ENTER USERNAME";
        $password = "ENTER PASSWORD";

        // connect using MySQLi object-oriented
        $this->conn = new mysqli($serverName, $userName, $password, $dbname);

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        /*
        // connect using PDO
        try{
            $this->conn = new PDO("mysqp:host=$serverName;dbname=$dbname", $userName, $password);

            // set exception mode
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        } catch(PDOException $e) {
            echo "<p style=\"color:red;\">" . $e->getMessage() . "</p><br>";
        }
        */


    }

    function closeConnection(){

        // close (MySQLi)
        $this->conn->close();

        /*
        // close (PDO)
        $this->conn = null;
        */
    }


}



?>