<html>
<head>
   <script LANGUAGE="javascript">
        // check to make sure all required fields are nonempty
        function check_all_fields(form_obj){    
            if(form_obj.patronnum.value == "" && form_obj.username.value == "") {
                alert("Either patron number or name field should be nonempty");
                return false;
            }
            return true;
        }
    </script>

    <style>
        body{
            background-color: black;
            color: white;
        }
        a{
            color: white;
        }
    </style>

</head>
<body>

<?php

    // include our backend classes
    require "connector.php";
    require "manager.php";

    // if no form has been submitted yet, load the submission form html
    if( $_POST == null ){
?>

<h2>Patron Record:</h2>
<form name="search" method=post onsubmit="return check_all_fields(this)" action="patron_record.php">
    <input type=hidden name="searchAttribute" value="patronRecord">
    <em>Choose one:</em><br>
    Patron Number:
    <input type="text" name="patronnum" size=10><br>
    Name:
    <input type="text" name="username" size=10><br>
    <input type=submit>
</form>
<BR><BR>

<?php
    } else {
        // set up variables with content from form submission
        $patronnum = $_POST["patronnum"];
        $username = $_POST["username"];

        // instantiate backend classes, and connect to the database
        $connector = new Connector();
        $manager = new Manager();

        $connector->Connect();

        // tell the manager to run a query
        $manager->patronRecord($patronnum, $username, $connector->conn);

        // disconnect from the database
        $connector->closeConnection();
    }
?>

    <BR><a href="patron_record.php"> Get another patron record </a>
    <BR><a href="index.html"> Main menu </a></p>

</body>
</html>