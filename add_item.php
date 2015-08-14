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
                alert("Please select a Media Type");
                return false;
            }
            return true;
        }

        var numAuthors=1;

        // add author text box element
        function addAuthor()
        {
            numAuthors++;

            var authorParagraph = document.getElementById("authors");

            var authorHTML = document.createElement("div");
            authorHTML.setAttribute("id","author"+numAuthors+"section");
            authorHTML.innerHTML = "Author #"+numAuthors.toString()+": " +
                                    "<input type='text' name='author"+numAuthors.toString()+"' size=30>" +
                                    " Type: <select name='author"+numAuthors.toString()+"type'>" +
                                        "<option value=''>Select...</option>" +
                                        "<option value='Writer'>Writer</option>" +
                                        "<option value='Introduction'>Introduction</option>" +
                                        "<option value='Illustrator'>Illustrator</option> " +
                                    "</select>" +
                                    "<br>";

            authorParagraph.appendChild(authorHTML);
        }

        // remove author text box element
        function removeAuthor()
        {
           if(numAuthors > 1)
            {
                var authorParagraph = document.getElementById("authors");
                authorParagraph.removeChild(document.getElementById("author"+numAuthors+"section"));
                numAuthors--;
            }
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
        <input type="hidden" name="searchAttribute" value="addItem">
        <p>
          ISBN:
           <input type="text" name="isbn" size=40>
        </p>
        <p>
          Title (required):
          <input type="text" name="title" size=40>
        </p>

        <!-- DYNAMICALLY ADD TEXT BOXES TO ACCOMMODATE AN ARBITRARY NUMBER OF AUTHORS -->

        <p id="authors">
            Author #1:
            <input type="text" name="author1" size=30>
            Type:
            <select name="author1type">
                <option value="">Select...</option>
                <option value="Writer">Writer</option>
                <option value="Introduction">Introduction</option>
                <option value="Illustrator">Illustrator</option>
            </select>
            <br>
        </p>
        <p>
            <input type="button" onclick="addAuthor()" value="Add another author">
           <input type="button" onclick="removeAuthor()" value="Remove last author">
        </p>
        <p>
            MediaType (required):
            <select name="mediatype">
                <option value="">Select...</option>
                <option value="Hardcover book">Hardcover book</option>
                <option value="Paperback book">Paperback book</option>
                <option value="Magazine">Magazine</option>
                <option value="Zine">Zine</option>
                <option value="LP Record">LP Record</option>
                <option value="7-Inch Record">7" Record</option>
                <option value="CD">CD</option>
                <option value="CD-R">CD-R</option>
                <option value="Cassette tape">Cassette tape</option>
                <option value="VHS">VHS</option>
                <option value="Laserdisc">Laserdisc</option>
                <option value="DVD">DVD</option>
                <option value="Blu-Ray">Blu-Ray</option>

            </select>
        </p>
        <p>
            Publication Date:
            <input type="text" name="pubdate" size=20>
        </p>
        <p>
            Condition:
            <input type="text" name="condition" size=20>
        </p>
        <p>
            Notes:
            <input type="text" name="notes" size=50>
        </p>
        <p>
            Shelf Location:
            <input type="text" name="shelfloc" size=20>
        </p>
        <p>
            API Link:
            <input type="text" name="apilink" size=40>
        </p>

        <p>
            <input type=submit>
        </p>
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
    $apilink = $_POST["apilink"];

    $authors = array();
    $authortypes = array();

    $i = 1;
    while (isset($_POST["author" . $i]))
    {
        $authors[] = $_POST["author" . $i];
        $authortypes[] = $_POST["author" . $i . "type"];
        $i++;
    }

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