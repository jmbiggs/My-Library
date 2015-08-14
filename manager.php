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

        if (!$ps = $con->prepare($query))
        {
            if (empty ($con))
                echo "Error: No connection established.";
            else
                echo "Error: " . $con->error;
            return;
        }


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

        // setting timezone to Denver time- change if you want
        // supported timezones: http://php.net/manual/en/timezones.php
        date_default_timezone_set("America/Denver");

        $date = time();
        $AqDate = date('Y-m-d', $date);

        // set up and execute query

        $query = "INSERT INTO Item (ISBN, ItemCondition, MediaType, AcquireDate, Notes, Title, ShelfLoc, PubDate, APILink) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

        if (!$ps = $con->prepare($query))
        {
            if (empty ($con))
                echo "Error: No connection established.";
            else
                echo "Error: " . $con->error;
            return;
        }

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

        if (isset($Authors) && is_array($Authors)) {
            for ($i = 0; $i < count($Authors); $i++) {

                $query = "INSERT INTO Authored (Author, ItemNo, AuthType) VALUES (?, ?, ?)";
                if (!$ps = $con->prepare($query)) {
                    echo "Error: " . $con->error;
                    return;
                }


                $author = $Authors[$i];
                $authortype = $AuthorTypes[$i];

                // set up and execute query (using MySQLi)
                $ps->bind_param("sis", $author, $generated_id, $authortype);

                if ($ps->execute() === FALSE) {
                    echo "Error: " . $query . "<br>" . $con->error;
                    return;
                }
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

    /*
     * displays information about patron with given patronNo OR username
     */
    public function patronRecord($patronNo, $username, $con)
    {

        // Start by retrieving card number, username, and email address

        // set up variables to store this info
        $retrievedPatronNo = null;
        $retrievedUsername = null;
        $retrievedEmail = null;

        // set up query
        if (isset($patronNo)) // look up record by patron number
        {
            //$query = "SELECT * FROM Patron WHERE PatronNo = ?"; // SAFER --- FIGURE OUT HOW TO DO IT THIS WAY!!
            $query = "SELECT * FROM Patron WHERE PatronNo = '" . $patronNo . "'";
        }
        else // look up record by username
        {
            //$query = "SELECT * FROM Patron WHERE Name = ?";
            $query = "SELECT * FROM Patron WHERE Name = '" . $username . "'";
        }

        /*

        // set up prepared statement
        if (!$ps = $con->prepare($query))
        {
            if (empty ($con))
                echo "Error: No connection established.";
            else
                echo "Error: " . $con->error;
            return;
        }

        // bind variables to query (using MySQLi)
        if (isset($patronNo)) {
            $ps->bind_param("i", $patronNo);
        }
        else {
            $ps->bind_param("s", $username);
        }

        */

        // execute query and get results
        $result = $con->query($query);

        if (!$result) {
            echo "Error: " . $query . "<br>" . $con->error;
            return;
        }
        elseif ($result->num_rows > 0){
            $row = $result->fetch_assoc();
            $retrievedPatronNo = $row["PatronNo"];
            $retrievedUsername = $row["Name"];
            $retrievedEmail = $row["Email"];
            $result->close();
        } else {
            echo "No patron found with Patron Number: " . $patronNo . " Username: " . $username . "<br>";
            $result->close();
            return;
        }


        // TODO: get info about checked out books


        // Print results

        echo "Name: " . $retrievedUsername . "<br>";
        echo "Patron number: " . $retrievedPatronNo . "<br>";
        echo "Email: " . $retrievedEmail . "<br>";

    }

}






/*
 *  MICHAEL BIGGS
 *  5-19-2015
 */





?>