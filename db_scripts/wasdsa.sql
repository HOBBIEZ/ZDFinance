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
    Address VARCHAR(255) NOT NULL
);

CREATE TABLE Accounts (
    IBAN BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    UserID INT NOT NULL,
    Transfer_Limit INT UNSIGNED NOT NULL,
    Balance DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
    Creation_Date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    Status ENUM('active', 'inactive', 'blocked') NOT NULL DEFAULT 'active',
    FOREIGN KEY (UserID) REFERENCES Users(UserID) ON DELETE CASCADE
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
    Status ENUM('active', 'inactive', 'blocked') NOT NULL,
    Expiration_Date DATE GENERATED ALWAYS AS (DATE_ADD(Creation_Date, INTERVAL 4 YEAR)) STORED,
    PRIMARY KEY (Card_Number, CVV),
    FOREIGN KEY (UserId) REFERENCES Users(UserID) ON DELETE CASCADE,
    FOREIGN KEY (IBAN) REFERENCES Accounts(IBAN) ON DELETE CASCADE
) AUTO_INCREMENT = 100000000;

CREATE TABLE Audit_Logs (
    LogID BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    UserID INT NOT NULL,
    Type ENUM('login', 'logout', 'signup', 'delete', 'acc_crt', 'acc_del', 'card_crt', 'card_del', 'transaction') NOT NULL,
    Timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (UserID) REFERENCES Users(UserID) ON DELETE CASCADE
);

INSERT INTO Users (Username, Password, First_Name, Last_Name, Date_of_Birth, Gender, Email, Phone_Number, Address)
VALUES
('johndoe', 'hashed_password_1', 'John', 'Doe', '1990-01-01', 'male', 'johndoe@example.com', '+1234567890', '123 Elm Street'),
('alicew', 'hashed_password_2', 'Alice', 'Williams', '1995-05-15', 'female', 'alicew@example.com', '+1234567891', '456 Oak Avenue'),
('bwayne', 'hashed_password_3', 'Bruce', 'Wayne', '1985-11-10', 'male', 'bwayne@example.com', '+1234567892', '100 Gotham Way'),
('clarkk', 'hashed_password_4', 'Clark', 'Kent', '1988-07-18', 'male', 'clarkk@example.com', '+1234567893', 'Metropolis Ave'),
('diana', 'hashed_password_5', 'Diana', 'Prince', '1992-03-21', 'female', 'diana@example.com', '+1234567894', 'Amazon Street');


INSERT INTO Accounts (UserID, Transfer_Limit, Balance, Status)
VALUES
(1, 5000, 10500.75, 'active'),
(2, 7000, 8700.50, 'active'),
(3, 10000, 15000.00, 'active'),
(4, 8000, 9200.25, 'active'),
(5, 6000, 5800.00, 'inactive');

INSERT INTO External_Transactions (TransactionID, UserID, Type, Amount, Timestamp, Status, External_Account_Address)
VALUES
(1, 1, 'withdraw', 200.00, CURRENT_TIMESTAMP, 'Completed', 'EXTACC12345'),
(2, 2, 'withdraw', 450.50, CURRENT_TIMESTAMP, 'Completed', 'EXTACC67890'),
(3, 3, 'withdraw', 1200.00, CURRENT_TIMESTAMP, 'Failed', 'EXTACC11223'),
(4, 4, 'deposit', 750.25, CURRENT_TIMESTAMP, 'Completed', 'EXTACC33445'),
(5, 5, 'deposit', 500.00, CURRENT_TIMESTAMP, 'Pending', 'EXTACC55667');

INSERT INTO Internal_Transactions (TransactionID, UserID, From_IBAN, To_IBAN, Amount, Timestamp, Status)
VALUES
(1, 1, 1000000000000000, 1000000000000001, 1500.00, CURRENT_TIMESTAMP, 'Completed'),
(2, 2, 1000000000000001, 1000000000000002, 500.00, CURRENT_TIMESTAMP, 'Completed'),
(3, 3, 1000000000000002, 1000000000000003, 2000.00, CURRENT_TIMESTAMP, 'Pending'),
(4, 4, 1000000000000003, 1000000000000004, 4500.00, CURRENT_TIMESTAMP, 'Completed'),
(5, 5, 1000000000000004, 1000000000000000, 250.75, CURRENT_TIMESTAMP, 'Completed');

INSERT INTO Cards (CVV, UserID, IBAN, PIN, Purchase_Limit, Status, Expiration_Date)
VALUES
(123, 1, 1000000000000000, 1111, 3000, 'Active', '2025-12-31'),
(456, 2, 1000000000000001, 2222, 5000, 'Active', '2026-01-15'),
(789, 3, 1000000000000002, 3333, 4000, 'Blocked', '2024-09-01'),
(321, 4, 1000000000000003, 4444, 2500, 'Active', '2025-08-31'),
(654, 5, 1000000000000004, 5555, 2000, 'Inactive', '2023-07-31');

INSERT INTO Audit_Logs (LogId, UserID, Type, Timestamp)
VALUES
(1, 1, 'Login', CURRENT_TIMESTAMP),
(2, 2, 'Transaction', CURRENT_TIMESTAMP),
(3, 3, 'Login', CURRENT_TIMESTAMP),
(4, 4, 'Card Issued', CURRENT_TIMESTAMP),
(5, 5, 'Account Update', CURRENT_TIMESTAMP);

DELIMITER $$
CREATE TRIGGER before_insert_cards
BEFORE INSERT ON Cards
FOR EACH ROW
BEGIN
    IF NEW.CVV IS NULL THEN
        SET NEW.CVV = (SELECT IFNULL(MAX(CVV), -1) + 1 FROM Cards) % 1000;
    END IF;
END$$
DELIMITER ;


DELIMITER $$
CREATE TRIGGER check_age_before_insert
BEFORE INSERT ON Users
FOR EACH ROW
BEGIN
    IF NEW.Date_of_Birth > CURDATE() - INTERVAL 18 YEAR THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'You must be at least 18 years old.';
    END IF;
END$$
DELIMITER ;