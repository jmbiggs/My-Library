<?php

/*
 *  MICHAEL BIGGS
 *  5-19-2015
 */


class Manager {

    /*
     * add a patron record into the database
     */
    public function addPatron($name, $email, $con){

        // set up and execute query

        $query = "INSERT INTO Patron (name, email) VALUES (?, ?)";

        $ps = $con->prepare($query);

        // set up and execute query (using MySQLi)
        $ps->bind_param("ss", $name_, $email_);

        $name_ = $name;
        $email_ = $email;

        if ($ps->execute() === TRUE) {
            echo "Created patron: " . $name . "<br>";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }

        $ps->close();

        /*
        // set up and execute query (using PDO)

        $ps->bindParam(1, $name);
        $ps->bindParam(2, $email);

        try {
            $ps->execute();
            echo "Created patron: " . $name . "<br>";

        } catch (PDOException $e) {
            echo "Unable to execute query: " . $query . "<br>" . $e->getMessage() . "<br>";
        }
        */

    }





}






/*
 *  MICHAEL BIGGS
 *  5-19-2015
 */





?>