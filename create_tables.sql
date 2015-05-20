CREATE TABLE Patron (
	PatronNo int AUTO_INCREMENT,
	Name TINYTEXT NOT NULL,
	Email TINYTEXT,
	PRIMARY KEY (PatronNo)
)

CREATE TABLE Item (
	ItemNo int AUTO_INCREMENT,
	ISBN tinytext NOT NULL,
	ItemCondition tinytext,
	MediaType tinytext NOT NULL,
	AquireDate date,
	Notes mediumtext,
	Title tinytext NOT NULL,
	ShelfLoc tinytext,
	PubDate date,
	APILink tinytext,
	PRIMARY KEY (ItemNo)
)

CREATE TABLE Authored (
	A_ID int AUTO_INCREMENT,
	Author tinytext NOT NULL,
	AuthType tinytext,
	ItemNo int NOT NULL,
	PRIMARY KEY(A_ID),
	FOREIGN KEY(ItemNo) REFERENCES Item(ItemNo)
)
	
CREATE TABLE CheckOut (
	CheckOutID int AUTO_INCREMENT,
	DateIn datetime,
	DateOut datetime,
	DueDate date,
	PatronNo int NOT NULL,
	ItemNo int NOT NULL,
	PRIMARY KEY(CheckOutID),
	FOREIGN KEY(PatronNo) REFERENCES Patron(PatronNo),
	FOREIGN KEY(ItemNo) REFERENCES Item(ItemNo)
)


