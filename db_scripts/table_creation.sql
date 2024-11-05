DROP TABLE IF EXISTS Internal_Transactions;
DROP TABLE IF EXISTS Cards;
DROP TABLE IF EXISTS Audit_Logs;
DROP TABLE IF EXISTS External_Transactions;
DROP TABLE IF EXISTS Accounts;
DROP TABLE IF EXISTS Users;

CREATE TABLE Users (
    UserID INT NOT NULL PRIMARY KEY,
    Username VARCHAR(32) NOT NULL,
    Password VARCHAR(32) NOT NULL,
    Creation_Date TIMESTAMP NOT NULL,
    First_Name VARCHAR(64) NOT NULL,
    Last_Name VARCHAR(64) NOT NULL,
    Date_of_Birth DATE,
    Gender TINYINT(1) NOT NULL, -- 0 -> male 1 -> female
    Email VARCHAR(64),
    Phone_Number VARCHAR(32),
    Address VARCHAR(64)
);

CREATE TABLE Accounts (
    IBAN VARCHAR(32) NOT NULL PRIMARY KEY,
    UserID INT NOT NULL,
    Transfer_Limit INT NOT NULL,
    Type VARCHAR(32) NOT NULL,
    Balance REAL,
    Creation_Date TIMESTAMP NOT NULL,
    Status VARCHAR(32) NOT NULL,
    FOREIGN KEY (UserID) REFERENCES USERS(UserID)
);

CREATE TABLE External_Transactions (
	TransactionID INT NOT NULL PRIMARY KEY,
    UserID INT NOT NULL,
    Type VARCHAR(32) NOT NULL,
    Amount REAL,
    Timestamp TIMESTAMP NOT NULL,
    Status VARCHAR(32) NOT NULL,
    External_Account VARCHAR(32) NOT NULL,
    FOREIGN KEY (UserID) REFERENCES Users(UserID)
);

CREATE TABLE Internal_Transactions (
	TransactionID INT NOT NULL PRIMARY KEY,
    UserID INT NOT NULL,
    From_IBAN VARCHAR(32) NOT NULL,
    To_IBAN VARCHAR(32) NOT NULL,
    Description VARCHAR(256),
    Amount REAL,
    Timestamp TIMESTAMP NOT NULL,
    Status VARCHAR(32) NOT NULL,
    FOREIGN KEY (UserID) REFERENCES Users(UserID),
    FOREIGN KEY (From_IBAN) REFERENCES Accounts(IBAN),
    FOREIGN KEY (To_IBAN) REFERENCES Accounts(IBAN)
);

CREATE TABLE Cards (
    Card_Number VARCHAR(32) NOT NULL,
    CVV INT NOT NULL,
    UserID INT NOT NULL,
    IBAN VARCHAR(32) NOT NULL,
    PIN INT NOT NULL,
    Purchase_Limit INT NOT NULL,
    Status VARCHAR(32) NOT NULL,
    Expiration_Date DATE NOT NULL,
    PRIMARY KEY (Card_Number, CVV),
    FOREIGN KEY (UserId) REFERENCES Users(UserID),
    FOREIGN KEY (IBAN) REFERENCES Accounts(IBAN)
);

CREATE TABLE Audit_Logs (
    Log_Id INT NOT NULL PRIMARY KEY,
    UserID INT NOT NULL,
    Type VARCHAR(32) NOT NULL,
    Description TEXT,
    Timestamp TIMESTAMP NOT NULL,
    Status VARCHAR(32) NOT NULL,
    FOREIGN KEY (UserID) REFERENCES Users(UserID)
);
