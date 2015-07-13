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
    public function addItem($ISBN, $ItemCondition, $MediaType, $Notes, $Title, $ShelfLoc, $PubDate, $APILink, $Authors, $AuthorTypes, $con)
    {
        // get a date timestamp
        $date = date_default_timezone_get();
        $AqDate = date('Y-m-d', strtotime(str_replace('-', '/', $date)));

        // set up and execute query

        $query = "INSERT INTO Item (ISBN, ItemCondition, MediaType, AquireDate, Notes, Title, ShelfLoc, PubDate, APILink) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $ps = $con->prepare($query);

        // set up and execute query (using MySQLi)
        $ps->bind_param("sssssssss", $ISBN, $ItemCondition, $MediaType, $AqDate, $Notes, $Title, $ShelfLoc, $PubDate, $APILink);

        $generated_id = -1;
        if ($ps->execute() === TRUE) {
            $generated_id = $con->insert_id;
        } else {
            echo "Error: " . $query . "<br>" . $con->error;
            return;
        }

        $ps->close();

        // now deal with authors

        for ($i=0; $i<$Authors->count(); $i++){

            $query = "INSERT INTO Authored (Author, ItemNo, AuthType) VALUES (?, ?, ?)";
            $ps = $con->prepare($query);

            $author = $Authors[$i];
            $authortype = $AuthorTypes[$i];

            // set up and execute query (using MySQLi)
            $ps->bind_param("sis", $author, $generated_id, $authortype);

            if ($ps->execute() === FALSE) {
                echo "Error: " . $query . "<br>" . $con->error;
                return;
            }
        }

        echo $Title . " successfully added. <br><br> Assigned ID number: " . $generated_id . "<br>";
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