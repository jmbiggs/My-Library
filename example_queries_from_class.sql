
-- TABLES --

CREATE TABLE Book( 	ISBN  VARCHAR(20),
				title TINYTEXT,				
				publisher TINYTEXT,
				pubYear YEAR,
				subject TINYTEXT,
				format TINYTEXT,
				summary TEXT,
				PRIMARY KEY (ISBN) );
				
CREATE TABLE Authored( a_id INT(20) AUTO_INCREMENT,
				author TINYTEXT,
				ISBN VARCHAR(20),
				PRIMARY KEY (a_id),
				FOREIGN KEY (ISBN) REFERENCES Book (ISBN) );				
				
CREATE TABLE Item( 	itemNo INT(10) AUTO_INCREMENT,
				location TINYTEXT,
				availability TINYTEXT,
				lostDate DATETIME,
				ISBN VARCHAR(20) NOT NULL,
				PRIMARY KEY (itemNo),
				FOREIGN KEY (ISBN) REFERENCES Book (ISBN) );
								
CREATE TABLE Patron( username CHAR(15),
				cardNum INT(10) UNIQUE,
				name TINYTEXT,
				phone TINYTEXT,
				email TINYTEXT,
				address TINYTEXT,
				PRIMARY KEY (username) );

CREATE TABLE Review( username CHAR(15),
			ISBN VARCHAR(20),
			text TEXT,
			score INT(2) CHECK (score>=1 AND score <=10),
			reviewDate DATETIME,
			PRIMARY KEY (username, ISBN),
			FOREIGN KEY (username) REFERENCES Patron (username),
			FOREIGN KEY (ISBN) REFERENCES Book (ISBN) );
			
CREATE TABLE Request( RID INT(15) AUTO_INCREMENT,
			username CHAR(15),
			ISBN VARCHAR(20),
			datePlaced DATETIME,		
			active BOOLEAN,
			PRIMARY KEY (RID),
			FOREIGN KEY (username) REFERENCES Patron (username),
			FOREIGN KEY (ISBN) REFERENCES Book (ISBN) );

CREATE TABLE CheckOut( COID INT(15) AUTO_INCREMENT,
			username CHAR(15),
			itemNo INT(10),
			inDate DATETIME,		
			outDate DATETIME,		
			dueDate DATETIME,	
			PRIMARY KEY (COID),
			FOREIGN KEY (username) REFERENCES Patron (username),
			FOREIGN KEY (itemNo) REFERENCES Item (itemNo) );

--////////// #1 REGISTRATION ////////--

INSERT INTO Patron (username, cardNum, name, phone, email, address)
VALUES (%u, GENERATED CARD #, %n, %p, %e, %a);

--find largest card number
SELECT MAX(cardNum)
FROM Patron

--///////// #2 CHECK OUT BOOK ///////--

----first make sure it's available
SELECT availability
FROM Item
WHERE itemNo = %i;

----if availability is HELD then 
----- find who it is held for

-------- !!!!! NOT WORKING 
   ----------- FIXED I THINK
 
SELECT r.username, r.RID
FROM Request r, Item i
WHERE i.ISBN = r.ISBN
AND i.itemNo = %i
AND r.active = true
AND r.datePlaced <= ALL (SELECT r1.datePlaced
							FROM Request r1
							WHERE i.ISBN = r1.ISBN
							AND i.itemNo = %i);
							
---- now proceed
INSERT INTO CheckOut(username, itemNo, outDate, dueDate)
VALUES (%u, %i, currentDATE, currentDATE+30);

UPDATE Item
SET availability = 'OUT'
WHERE itemNo = %i;

----delete request if necessary
	--- need to get isbn
UPDATE Request
SET active = false
WHERE 
WHERE itemNo = ?
AND username = %u


--///////// #3 GET ON WAIT LIST //////--

---- probably should check first to make sure there isn't a request already
INSERT INTO Request (username, ISBN, datePlaced, active)
VALUES (%u, %i, currentDATE, true);

---- get availability
SELECT availability, itemNo
FROM Item
WHERE ISBN = ?;

--- if it's available, set it to HELD
UPDATE Item
SET availability = 'HELD'
WHERE itemNo = %i;



--///////// #4 USER RECORD //////////--

-- personal data:
--username (given cardnum):
SELECT username
FROM Patron
WHERE cardNum = %c;

--cardnum (given username):
SELECT cardNum
FROM Patron
WHERE username = %u;

--name, phone, email, address
SELECT name, phone, email, address
FROM Patron
WHERE username = %u;

--- full history of books checked out in the past
SELECT i.ISBN, b.title, c.outDate, c.inDate
FROM Item i, Book b, CheckOut c
WHERE c.username = %u
AND c.itemNo = i.itemNo
AND i.ISBN = b.ISBN;

--- full list of books lost by user
SELECT i.ISBN, b.title, c.outDate, i.lostDate
FROM Item i, Book b, CheckOut c
WHERE c.username = %u
AND c.itemNo = i.itemNo
AND i.ISBN = b.ISBN
AND i.availability = 'LOST';

--- full list of requests
SELECT r.ISBN, b.title, r.datePlaced
FROM Book b, Request r
WHERE r.username = %u
AND r.ISBN = b.ISBN
AND r.active = true;

--- full list of reviews
SELECT r.ISBN, b.title, r.score, r.text
FROM Review r, Book b
WHERE r.username = %u
AND r.ISBN = b.ISBN;


--////////// #5 NEW BOOK ////////--

INSERT INTO Book (ISBN, title, publisher, pubYear, subject, format, summary)
VALUES (%i, %t, %a, %p, %y, %sub, %f, %sum);

--- for each author
INSERT INTO Authored (ISBN, author) VALUES (?, ?);

--////////// #6 NEW ITEM ////////--

 ------ look to see if there is an active hold for ISBN
 
--- GET number of active holds on ISBN
SELECT COUNT(username)
FROM Request
WHERE ISBN = ?
AND active = true
 
  ------ if so, see if there are fewer items with ISBN marked held
	----- than there are people on wait list

--- GET number of items marked HELD
SELECT COUNT(itemNo)
FROM Item
WHERE ISBN = ?
AND availability = 'HELD'
	
---- add the item
INSERT INTO Item (location, availability, ISBN)
VALUES (%l, %a, %i);

--////////// #7 LATE BOOK LIST ////////--
 
SELECT b.ISBN, b.title, c.dueDate, c.username, p.name, p.phone, p.email
FROM CheckOut c, Book b, Patron p, Item i
WHERE c.inDate IS NULL
AND b.ISBN = i.ISBN
AND dueDate < %currentDATE
AND c.itemNo = i.itemNo
AND c.username = p.username;

--////////// #8 BOOK REVIEW ////////--

INSERT INTO Review (username, ISBN, text, score, reviewDate)
VALUES (%u, %i, %t, %s, currentDATE);

--////////// #9 BOOK BROWSING ////////--

--- search values: author, publisher, title-words, subject

SELECT i.itemNo, b.title, a.author, b.publisher, b.subject, i.location, i.availability
FROM Book b, Item i, Review r, CheckOut c, Authored a
WHERE b.ISBN = i.ISBN
AND b.ISBN = r.ISBN
AND a.ISBN = b.ISBN
AND i.itemNo = c.itemNo
AND a.author LIKE '%[%a]%'
AND b.publisher LIKE '%[%p]%'
AND b.title LIKE '%[%t]%'
AND b.subject LIKE '%[%s]%'
-- optional available only
AND i.availabilty = 'IN'
-- sort options:
-- year
ORDER BY b.pubYear DESC
-- avg score  (could be problematic since many may not have a score)
GROUP BY r.ISBN
ORDER BY AVG(r.score)
-- popularity (# of checkouts)
GROUP BY i.ISBN
ORDER BY COUNT(i.ISBN)


--////////// #10 RETURNING BOOK ////////--

---- mark lost
UPDATE Item
SET availability = 'LOST', lostDate = %currentDATE
WHERE itemNo = %i;

---- check in

UPDATE CheckOut
SET inDate = %currentDATE
WHERE itemNo = %i
AND inDate IS NULL;

	----get holds list
SELECT r.username, r.datePlaced
FROM Request r, Item i
WHERE i.itemNo = %i
AND i.ISBN = r.ISBN
AND r.active = true
ORDER BY r.datePlaced
	
	-- if hold
UPDATE Item
SET availability = 'HELD'
WHERE itemNo = %i;

	-- otherwise 
UPDATE Item
SET availability = 'IN'
WHERE itemNo = %i;


--////////// #11 BOOK RECORD ////////--

---- assume given ISBN

-- basic info
SELECT title, publisher, pubYear, subject, format, summary
FROM Book
WHERE ISBN = ?;

-- list of all copies
SELECT itemNo, location, availability
FROM Item
WHERE ISBN = ?;

-- checkout history
SELECT c.username, c.itemNo, c.outDate, c.inDate
FROM CheckOut c, Item i
WHERE i.ISBN = ?
AND i.itemNo = c.itemNo;

-- average review
SELECT AVG(score)
FROM Review
WHERE ISBN = ?;

--  individual reviews
SELECT text, score
FROM Review
WHERE ISBN = ?;




--////////// #12 BOOK STATISTICS ////////--

-- list of n most checked out books
SELECT b.title, COUNT(i.ISBN)
FROM CheckOut c, Item i, Book b
WHERE c.itemNo = i.itemNo
AND i.ISBN = b.ISBN
GROUP BY i.ISBN
ORDER BY COUNT(i.ISBN) DESC
LIMIT ?;

-- list of n most requested books
SELECT b.title, COUNT(r.ISBN)
FROM Request r, Book b
WHERE r.ISBN = b.ISBN
GROUP BY r.ISBN
ORDER BY COUNT(r.ISBN) DESC
LIMIT ?;

-- list of n most lost books
SELECT b.title, COUNT(i.ISBN)
FROM Item i, Book b
WHERE i.ISBN = b.ISBN
AND i.availability = 'LOST'
GROUP BY i.ISBN
ORDER BY COUNT(i.ISBN) DESC
LIMIT ?;

-- list of n most popular authors
SELECT a.author, COUNT(a.author)
FROM CheckOut c, Item i, Authored a
WHERE c.itemNo = i.itemNo
AND i.ISBN = a.ISBN
GROUP BY a.author
ORDER BY COUNT(a.author) DESC
LIMIT ?;


--////////// #12 USER STATISTICS ////////--

-- top n users who have checked out the most books
SELECT username, COUNT(itemNo)
FROM CheckOut
GROUP BY username
ORDER BY COUNT(itemNo) DESC
LIMIT ?;

-- top n users who have rated the most books
SELECT username, COUNT(ISBN)
FROM Review
GROUP BY username
ORDER BY COUNT(ISBN) DESC
LIMIT ?;

-- top n users who have lost the most books
SELECT c.username, COUNT(c.itemNo)
FROM CheckOut c, Item i
WHERE c.itemNo = i.itemNo
AND i.availability = 'Lost'
AND c.inDate IS NULL
GROUP BY c.username
ORDER BY COUNT(c.itemNo) DESC
LIMIT ?;

