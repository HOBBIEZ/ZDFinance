DROP TABLE IF EXISTS Internal_Transactions;
DROP TABLE IF EXISTS Cards;
DROP TABLE IF EXISTS Audit_Logs;
DROP TABLE IF EXISTS External_Transactions;
DROP TABLE IF EXISTS Accounts;
DROP TABLE IF EXISTS Users;

CREATE TABLE Users (
    UserID INT AUTO_INCREMENT PRIMARY KEY, 
    Username VARCHAR(32) NOT NULL UNIQUE CHECK (Username REGEXP '^[a-zA-Z0-9_]{3,32}$'), 
    Password VARCHAR(255) NOT NULL,
    Creation_Date TIMESTAMP DEFAULT CURRENT_TIMESTAMP, 
    First_Name VARCHAR(64) NOT NULL CHECK (First_Name REGEXP '^[A-Za-z]+$'), 
    Last_Name VARCHAR(64) NOT NULL CHECK (Last_Name REGEXP '^[A-Za-z]+$'), 
    Date_of_Birth DATE NOT NULL, 
    Gender ENUM('male', 'female') NOT NULL, 
    Email VARCHAR(64) NOT NULL UNIQUE CHECK (Email REGEXP '^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\\.[a-zA-Z]{2,}$'), 
    Phone_Number VARCHAR(15) NOT NULL UNIQUE CHECK (Phone_Number REGEXP '^\\+?[0-9]{7,15}$'), 
    Address VARCHAR(255) NOT NULL,
    Status Enum('active', 'deleted') NOT NULL DEFAULT 'active'
);

CREATE TABLE Accounts (
    IBAN BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    UserID INT NOT NULL,
    Transfer_Limit INT UNSIGNED NOT NULL,
    Balance DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
    Creation_Date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    Status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    FOREIGN KEY (UserID) REFERENCES Users(UserID)
) AUTO_INCREMENT = 1000000000000000;

CREATE TABLE External_Transactions (
	TransactionID BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    UserID INT NOT NULL,
    Type ENUM('withdraw', 'deposit') NOT NULL,
    Amount REAL NOT NULL CHECK (Amount >= 10),
    Timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    Status ENUM('pending', 'finished', 'rejected') NOT NULL,
    External_Account_Address VARCHAR(255) NOT NULL,
    FOREIGN KEY (UserID) REFERENCES Users(UserID)
);

CREATE TABLE Internal_Transactions (
	TransactionID BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    UserID INT NOT NULL,
    From_IBAN BIGINT UNSIGNED NOT NULL,
    To_IBAN BIGINT UNSIGNED NOT NULL CHECK (From_IBAN <> To_IBAN),
    Description VARCHAR(256),
    Amount DECIMAL(10, 2) NOT NULL CHECK (Amount >= 10),
    Timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    Status ENUM('pending', 'finished') NOT NULL,
    FOREIGN KEY (UserID) REFERENCES Users(UserID),
    FOREIGN KEY (From_IBAN) REFERENCES Accounts(IBAN),
    FOREIGN KEY (To_IBAN) REFERENCES Accounts(IBAN)
);

CREATE TABLE Cards (
    Card_Number BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    CVV INT,
    Creation_Date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UserID INT NOT NULL,
    IBAN BIGINT UNSIGNED NOT NULL,
    PIN INT NOT NULL CHECK (PIN BETWEEN 0 AND 9999),
    Purchase_Limit INT NOT NULL CHECK (Purchase_Limit >= 10),
    Status ENUM('active', 'inactive') NOT NULL,
    Expiration_Date DATE GENERATED ALWAYS AS (DATE_ADD(Creation_Date, INTERVAL 4 YEAR)) STORED,
    PRIMARY KEY (Card_Number, CVV),
    FOREIGN KEY (UserId) REFERENCES Users(UserID),
    FOREIGN KEY (IBAN) REFERENCES Accounts(IBAN)
) AUTO_INCREMENT = 100000000;

CREATE TABLE Audit_Logs (
    LogID BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    UserID INT NOT NULL,
    Type ENUM('login', 'logout', 'signup', 'delete', 'acc_crt', 'acc_del', 'card_crt', 'card_del', 'transaction') NOT NULL,
    Timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (UserID) REFERENCES Users(UserID)
);
