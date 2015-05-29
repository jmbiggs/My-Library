# My Library

A simple personal library management system designed to help you organize your media collection, and keep track of who you lend things to.

The basic idea is to store collection information in a MySQL database accessible via a PHP web interface.

# Requirements

A MySQL database and a web server equipped with PHP.

# Database storage

## Items

Items will consist of
* ID Number (automatically assigned)
* ISBN (or other uniquely identifying number)
* Media Type (see below)
* Title
* Authors (one or more, can include musicians, actors, etc.)
and optionally:
* Item Condition
* Acquisition Date (or date added into database)
* Shelf Location
* Publication Date
* Notes (such as autographed copy, etc.)
* API Link (see below)

### Media Types

Supported media types will be customizable by the user.
Example media types:
* Hardcover book
* Paperback book
* Magazine
* Zine
* LP Record
* CD
* CD-R
* Cassette Tape
* VHS
* Laserdisc
* DVD
* Blu-Ray

### API Support

Certain media types will be able to have additional information pulled from existing APIs.
(Discogs, Open Library, IMDB, etc.)

## Patrons

Patrons will be stored simply as a 
* Name
* Email Address
* ID Number (Automatically Assigned)

# Intended functionality:

* Register new patron
* Add item
* Search through items
* Display all items
* Check out item to patron
* Retrieve patron check out information
* Email patrons with overdue items

# TO DO:

* Finish manager php file
* Finish constructing webpages
* Learn how to use CSS to simplify web formatting
* Learn how to store database access information (username, password, etc) securely, instead of in plain text in the PHP file.
* Learn how to pull information from APIs
* Write instructions on how to use


