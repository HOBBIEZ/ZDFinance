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
    Status ENUM('active', 'inactive', 'blocked') NOT NULL,
    Expiration_Date DATE GENERATED ALWAYS AS (DATE_ADD(Creation_Date, INTERVAL 4 YEAR)) STORED,
    PRIMARY KEY (Card_Number, CVV),
    FOREIGN KEY (UserId) REFERENCES Users(UserID),
    FOREIGN KEY (IBAN) REFERENCES Accounts(IBAN)
) AUTO_INCREMENT = 100000000;


CREATE TABLE Audit_Logs (
    Log_Id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    UserID INT NOT NULL,
    Type ENUM('login', 'logout', 'signup', 'delete', 'acc_crt', 'acc_del', 'card_crt', 'card_del', 'transaction') NOT NULL,
    Timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (UserID) REFERENCES Users(UserID)
);



DELIMITER $$
CREATE TRIGGER delete_accounts_on_user_delete
AFTER DELETE ON Users
FOR EACH ROW
BEGIN
    DELETE FROM Cards WHERE IBAN IN (SELECT IBAN FROM Accounts WHERE UserID = OLD.UserID);
    DELETE FROM Accounts WHERE UserID = OLD.UserID;
END$$
DELIMITER ;


DELIMITER $$
CREATE TRIGGER delete_cards_on_accounts_delete
AFTER DELETE ON Accounts
FOR EACH ROW
BEGIN
    DELETE FROM Cards WHERE IBAN = OLD.IBAN;
END$$
DELIMITER ;


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



-- Insert 5 sample users
INSERT INTO Users (Username, Password, First_Name, Last_Name, Date_of_Birth, Gender, Email, Phone_Number, Address) VALUES
('johndoe', 'password123', 'John', 'Doe', '1990-01-15', 'male', 'johndoe@example.com', '+12345678901', '123 Main St'),
('janedoe', 'password456', 'Jane', 'Doe', '1992-05-22', 'female', 'janedoe@example.com', '+19876543210', '456 Elm St'),
('alice99', 'password789', 'Alice', 'Smith', '1985-07-12', 'female', 'alice@example.com', '+10293847566', '789 Maple Ave'),
('bob_the_builder', 'builder123', 'Bob', 'Builder', '1980-03-25', 'male', 'bob@example.com', '+56473829101', '101 Cedar Ln'),
('eve_adams', 'eve123', 'Eve', 'Adams', '1995-09-30', 'female', 'eve@example.com', '+83920174652', '202 Birch Rd');

-- Insert 5 sample accounts
INSERT INTO Accounts (UserID, Transfer_Limit, Balance, Status) VALUES
(1, 5000, 1000.50, 'active'),
(2, 10000, 500.75, 'inactive'),
(3, 2000, 1200.00, 'active'),
(4, 7500, 800.25, 'blocked'),
(5, 10000, 300.00, 'active');

-- Insert 5 sample external transactions
INSERT INTO External_Transactions (UserID, Type, Amount, Status, External_Account_Address) VALUES
(1, 'deposit', 100.00, 'finished', 'account123'),
(2, 'withdraw', 50.00, 'rejected', 'account456'),
(3, 'deposit', 300.00, 'pending', 'account789'),
(4, 'withdraw', 200.00, 'finished', 'account101'),
(5, 'deposit', 150.00, 'pending', 'account202');

-- Insert 5 sample internal transactions
INSERT INTO Internal_Transactions (UserID, From_IBAN, To_IBAN, Description, Amount, Status) VALUES
(1, 1000000000000000, 1000000000000001, 'Transfer to Jane', 100.00, 'finished'),
(2, 1000000000000001, 1000000000000002, 'Rent payment', 200.00, 'pending'),
(3, 1000000000000002, 1000000000000003, 'Payment for Alice', 50.00, 'finished'),
(4, 1000000000000003, 1000000000000004, 'Bill payment', 75.00, 'finished'),
(5, 1000000000000004, 1000000000000000, 'Transfer to John', 25.00, 'pending');

-- Insert 5 sample cards
INSERT INTO Cards (CVV, UserID, IBAN, PIN, Purchase_Limit, Status) VALUES
(123, 1, 1000000000000000, 1111, 1000, 'active'),
(456, 2, 1000000000000001, 2222, 500, 'inactive'),
(789, 3, 1000000000000002, 3333, 2000, 'active'),
(101, 4, 1000000000000003, 4444, 1500, 'blocked'),
(202, 5, 1000000000000004, 5555, 1000, 'active');

-- Insert 5 sample audit logs
INSERT INTO Audit_Logs (UserID, Type) VALUES
(1, 'login'),
(2, 'signup'),
(3, 'acc_crt'),
(4, 'transaction'),
(5, 'logout');
