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

            // check all author fields to make sure that if one is filled in (i.e. author name),
            //  then the other one is too (i.e. author type)
            for (i = 1; i<=numAuthors; i++) {
                if (form_obj["author" + i].value == "" && form_obj["author" + i + "type"].value != "")
                {
                    alert("Author name #" + i + " should be nonempty");
                    return false;
                }
                else if (form_obj["author" + i].value != "" && form_obj["author" + i + "type"].value == "")
                {
                    alert("Author type #" + i + " should be nonempty");
                    return false;
                }
            }

            return true;
        }

        var numAuthors=1;

        // add author text box element
        function addAuthor()
        {
          //  if (form_obj["author" + numAuthors].value == "" && form_obj["author" + numAuthors + "type"].value == "") // don't add a row if the last line is blank
            //    return;

            numAuthors++;

            var authorParagraph = document.getElementById("authors");

            var authorHTML = document.createElement("div");
            authorHTML.setAttribute("id","author"+numAuthors+"section");
            authorHTML.innerHTML = "Author #"+numAuthors.toString()+": " +
                                    "<input type='text' name='author"+numAuthors.toString()+"' id='author"+numAuthors.toString()+"' size=30>" +
                                    " Type: <select name='author"+numAuthors.toString()+"type' id='author"+numAuthors.toString()+"type'>" +
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
           if(numAuthors > 1) // don't remove the very last one!
            {
                var authorParagraph = document.getElementById("authors");
                authorParagraph.removeChild(document.getElementById("author"+numAuthors+"section"));
                numAuthors--;
            }
        }

        // removes all authors
        function removeAllAuthors()
        {
            for (var i = 0; i < numAuthors; i++)
            {
                removeAuthor();
            }
        }

        // fill in certain input boxes with info from Open Library API
        function queryOpenLibrary(isbn)
        {
            // do nothing if nothing is in the ISBN text box
            if(isbn == "")
            {
                return;
            }

            // NOTE: I am using a CORS proxy (https://crossorigin.me/) in order to send the request to a different server.
            //  This is probably not a good long term solution, but the OL API doesn't appear to support CORS yet.

            var requestString = "https://crossorigin.me/" + "https://openlibrary.org/api/books?" + "format=json" + "&jscmd=data" + "&bibkeys=ISBN:" + encodeURIComponent(isbn);
            var request = new XMLHttpRequest();

            if ("withCredentials" in request) {
                request.open("GET", requestString, true);

                request.onreadystatechange = function () {
                    if (request.readyState === 4) {
                        if (request.status >= 200 && request.status < 400) {

                            var response = request.responseText;
                            var parsedResponse = JSON.parse(response);

                            if(Object.keys(parsedResponse).length === 0)
                            {
                                alert("No results found in OpenLibrary.");
                            }
                            else {
                                var bookInfo = parsedResponse["ISBN:" + isbn];

                                var title = bookInfo["title"];
                                var authors = bookInfo["authors"];
                                var date = bookInfo["publish_date"];
                                var apiLink = bookInfo["identifiers"]["openlibrary"][0];

                                //console.log("Title: " + title);
                                document.getElementById("title").value = title;

                                removeAllAuthors();
                                for (var i = 0; i < authors.length; i++) {
                                    //console.log("Author: " + authors[i]["name"]);
                                    if (i > 0)
                                    {
                                        addAuthor();
                                    }

                                    document.getElementById("author" + (i+1).toString()).value = authors[i]["name"];
                                    document.getElementById("author" + (i+1).toString() + "type").value = "Writer";
                                }

                                //console.log("Publication Date: " + date);
                                document.getElementById("pubdate").value = date;

                                //console.log("API Link: " + apiLink);
                                document.getElementById("apilink").value = "OpenLibrary: " + apiLink;
                            }
                        }
                        else {
                            alert("Problem connecting to OpenLibrary");
                        }
                    }
                };
                request.send();
            }
        }

        // fill in certain input boxes with info from Google Books API
        function queryGoogleBooks(isbn)
        {
            // do nothing if nothing is in the ISBN text box
            if(isbn == "")
            {
                return;
            }

            var requestString = "https://www.googleapis.com/books/v1/volumes?q=isbn:" + encodeURIComponent(isbn);
            var request = new XMLHttpRequest();

            if ("withCredentials" in request) {
                request.open("GET", requestString, true);

                request.onreadystatechange = function () {
                    if (request.readyState === 4) {
                        if (request.status >= 200 && request.status < 400) {

                            var response = request.responseText;
                            var parsedResponse = JSON.parse(response);

                            if(parsedResponse["totalItems"] === 0)
                            {
                                alert("No results found in Google Books.");
                            }
                            else {
                                var bookInfo = parsedResponse["items"][0]; // we're just going to take the first result

                                var title = bookInfo["volumeInfo"]["title"];
                                var authors = bookInfo["volumeInfo"]["authors"];
                                var date = bookInfo["volumeInfo"]["publishedDate"];
                                var apiLink = bookInfo["id"];

                                //console.log("Title: " + title);
                                document.getElementById("title").value = title;

                                removeAllAuthors();
                                for (var i = 0; i < authors.length; i++) {
                                    //console.log("Author: " + authors[i]);
                                    if (i > 0)
                                    {
                                        addAuthor();
                                    }

                                    document.getElementById("author" + (i+1).toString()).value = authors[i];
                                    document.getElementById("author" + (i+1).toString() + "type").value = "Writer";
                                }

                                //console.log("Publication Date: " + date);
                                document.getElementById("pubdate").value = date;

                                //console.log("API Link: " + apiLink);
                                document.getElementById("apilink").value = "GoogleBooks: " + apiLink;
                            }
                        }
                        else {
                            alert("Problem connecting to Google Books");
                        }
                    }
                };
                request.send();
            }
        }

        // fill in certain input boxes with info from Discogs API
        function queryDiscogs(isbn)
        {

            var token = document.getElementById("discogsToken").value;

            // do nothing if nothing is in the ISBN text box
            if(isbn == "" || token == "")
            {
                return;
            }

            // run a query to get the discogs id

            var requestString = "https://api.discogs.com/database/search?barcode=" + encodeURIComponent(isbn) + "&token=" + token;
            var request = new XMLHttpRequest();
            var apiLink = "";

            if ("withCredentials" in request) {
                request.open("GET", requestString, true);

                request.onreadystatechange = function () {
                    if (request.readyState === 4) {
                        if (request.status >= 200 && request.status < 400) {

                            var response = request.responseText;
                            var parsedResponse = JSON.parse(response);

                            if(parsedResponse["totalItems"] === 0)
                            {
                                alert("No results found on Discogs.");
                            }
                            else {
                                var mediaInfo = parsedResponse["results"][0]; // we're just going to take the first result
                                apiLink = mediaInfo["id"];
                                document.getElementById("apilink").value = "Discogs: " + apiLink;
                            }


                            // now, using the discogs id, make another query to get the other info

                            // return if we didn't get an id
                            if (apiLink == "") {
                                return;
                            }

                            var requestString2 = "https://api.discogs.com/releases/" + apiLink;
                            var request2 = new XMLHttpRequest();

                            if ("withCredentials" in request2) {
                                request2.open("GET", requestString2, true);

                                request2.onreadystatechange = function () {
                                    if (request2.readyState === 4) {
                                        if (request2.status >= 200 && request2.status < 400) {

                                            var response = request2.responseText;
                                            var parsedResponse = JSON.parse(response);

                                            var title = parsedResponse["title"];
                                            var authors = parsedResponse["artists"];
                                            var date = parsedResponse["released"];
                                            //var date = parsedResponse["year"];  // this one has just the year

                                            //console.log("Title: " + title);
                                            document.getElementById("title").value = title;

                                            removeAllAuthors();
                                            for (var i = 0; i < authors.length; i++) {
                                                //console.log("Author: " + authors[i]);
                                                if (i > 0)
                                                {
                                                    addAuthor();
                                                }

                                                document.getElementById("author" + (i+1).toString()).value = authors[i]["name"];
                                                document.getElementById("author" + (i+1).toString() + "type").value = "Writer";
                                            }

                                            //console.log("Publication Date: " + date);
                                            document.getElementById("pubdate").value = date;
                                        }
                                        else {
                                            alert("Problem connecting to Discogs");
                                        }
                                    }
                                };
                                request2.send();
                            }



                        }
                        else {
                            alert("Problem connecting to Discogs");
                        }
                    }
                };
                request.send();
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
require "credentials.php";

// if no form has been submitted yet, load the submission form html
if( $_POST == null ){

    $cred = new Credentials();
    echo '<input type="hidden" id="discogsToken" value="' . $cred->discogs_token . '"  />';

    ?>

    <h2>Add a new item:</h2>
    <form name="search" method=post onsubmit="return check_all_fields(this)" action="add_item.php">
        <input type="hidden" name="searchAttribute" value="addItem">


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
          ISBN:
           <input type="text" name="isbn" size=40>
            <input type="button" onclick="queryOpenLibrary(isbn.value)" value="Check with Open Library">
            <input type="button" onclick="queryGoogleBooks(isbn.value)" value="Check with Google Books">
            <input type="button" onclick="queryDiscogs(isbn.value)" value="Check with Discogs">
        </p>
        <p>
          Title (required):
          <input type="text" name="title" id="title" size=40>
        </p>

        <!-- DYNAMICALLY ADD TEXT BOXES TO ACCOMMODATE AN ARBITRARY NUMBER OF AUTHORS -->

        <p id="authors">
            Author #1:
            <input type="text" name="author1" id="author1" size=30>
            Type:
            <select name="author1type" id="author1type">
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
            Publication Date:
            <input type="text" name="pubdate" id="pubdate" size=20>
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
            <input type="text" name="apilink" id="apilink" size=40>
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
        if ($_POST["author" . $i] != NULL) { // ignore blank lines
            $authors[] = $_POST["author" . $i];
            $authortypes[] = $_POST["author" . $i . "type"];
        }
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
<BR><a href="index.html"> Main menu </a>

</body>
</html>