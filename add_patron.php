<html>
<head>

   <script LANGUAGE="javascript">

        // check to make sure all required fields are nonempty

        function check_all_fields(form_obj){
            //alert(form_obj.searchAttribute.value+"='"+form_obj.attributeValue.value+"'");
            if( form_obj.fname.value == ""){
                alert("Name field should be nonempty");
                return false;
            }
            else if( form_obj.email.value == ""){
                alert("Email field should be nonempty");
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

<h2>Register a new patron:</h2>
<form name="search" method=post onsubmit="return check_all_fields(this)" action="add_patron.php">
    <input type=hidden name="searchAttribute" value="addPatron">
    Full name:
    <input type="text" name="fname" length=10><br>
    Email address:
    <input type="text" name="email" length=10><br>
    <input type=submit>
</form>
<BR><BR>

<?php

    } else {

        // set up variables with content from form submission
        $name = $_POST["fname"];
        $email = $_POST["email"];

        // instantiate backend classes, and connect to the database
        $connector = new Connector();
        $manager = new Manager();

        $connector->Connect();

        // tell the manager to run a query
        $manager->addPatron($name, $email, $connector->conn);

        // disconnect from the database
        $connector->closeConnection();
    }

?>

    <BR><a href="add_patron.php"> Add another patron </a>
    <BR><a href="index.html"> Main menu </a></p>

</body>
</html>