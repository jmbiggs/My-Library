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
        $ps->bind_param("ss", $name, $email);

        if ($ps->execute() === TRUE) {
            $generated_id = $con->insert_id;
            echo $name . " successfully registered. <br><br> Assigned ID number: " . $generated_id . "<br>";
        } else {
            echo "Error: " . $query . "<br>" . $con->error;
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

    /*
     * add an item record into the database
     */
    public function addItem($ISBN, $ItemCondition, $MediaType, $AcquireDate, $Notes, $Title, $ShelfLoc, $PubDate, $APILink, $Authors, $AuthorTypes)
    {
        //TODO: how to deal with authors??

    }

    /*
     * checks an item out to given user, with given number of days as checkout time
     */
    public function checkOut($itemNo, $patronNo, $days)
    {

    }


}






/*
 *  MICHAEL BIGGS
 *  5-19-2015
 */





?>