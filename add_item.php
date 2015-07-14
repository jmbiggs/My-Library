<html>
<head>

    <script LANGUAGE="javascript">

        // check to make sure all required fields are nonempty

        function check_all_fields(form_obj){
            //alert(form_obj.searchAttribute.value+"='"+form_obj.attributeValue.value+"'");
            if( form_obj.title.value == ""){
                alert("Title field should be nonempty");
                return false;
            }
            else if( form_obj.mediatype.value == ""){
                alert("Media Type field should be nonempty");
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

    <h2>Add a new item:</h2>
    <form name="search" method=post onsubmit="return check_all_fields(this)" action="add_item.php">
        <input type=hidden name="searchAttribute" value="addItem">
        ISBN:
        <input type="text" name="isbn" length=40><br>
        Title (required):
        <input type="text" name="title" length=40><br>
        MediaType (required):
        <input type="text" name="mediatype" length=20><br>
        Publication Date:
        <input type="text" name="pubdate" length=20><br>
        Condition:
        <input type="text" name="condition" length=20><br>
        Notes:
        <input type="text" name="notes" length=50><br>
        Shelf Location:
        <input type="text" name="shelfloc" length=20><br>
        API Link:
        <input type="text" name="apilink" length=40><br>
        Author 1:
        <input type="text" name="author1" length=30><br>
        Author 1 Type:
        <input type="text" name="author1type" length=10><br>
        Author 2:
        <input type="text" name="author2" length=30><br>
        Author 2 Type:
        <input type="text" name="author2type" length=10><br>
        Author 3:
        <input type="text" name="author3" length=30><br>
        Author 3 Type:
        <input type="text" name="author3type" length=10><br>
        <input type=submit>
    </form>
    <BR><BR>

<?php

} else {

    // set up variables with content from form submission
    $isbn = $_POST["isbn"];
    $title = $_POST["title"];
    $mediatype = $_POST["mediatype"];
    $pubdate = $_POST["pubdate"];
    $condition = $_POST["condition"];
    $notes = $_POST["notes"];
    $shelf_loc = $_POST["shelfloc"];
    $spilink = $_POST["apilink"];
    $authors = array($_POST["author1"], $_POST["author2"], $_POST["author3"]);
    $authortypes = array($_POST["author1type"], $_POST["author2type"], $_POST["author3type"]);

    // instantiate backend classes, and connect to the database
    $connector = new Connector();
    $manager = new Manager();

    $connector->Connect();

    // tell the manager to run a query
    $manager->addItem($isbn, $condition, $mediatype, $notes, $title, $shelf_loc, $pubdate, $apilink, $authors, $authortypes, $connector->conn);

    // disconnect from the database
    $connector->closeConnection();
}

?>

<BR><a href="add_item.php"> Add another item </a>
<BR><a href="index.html"> Main menu </a></p>

</body>
</html>