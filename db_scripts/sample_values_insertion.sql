-- Insert dummy data for Users table
INSERT INTO Users (Username, Password, First_Name, Last_Name, Date_of_Birth, Gender, Email, Phone_Number, Address) VALUES
('john_doe', 'hashedpassword123', 'John', 'Doe', '1985-05-15', 'male', 'john.doe@example.com', '+1234567890', '123 Elm Street, Springfield, USA'),
('jane_smith', 'hashedpassword456', 'Jane', 'Smith', '1990-08-25', 'female', 'jane.smith@example.com', '+0987654321', '456 Oak Avenue, Springfield, USA'),
('bob_brown', 'hashedpassword789', 'Bob', 'Brown', '1975-03-10', 'male', 'bob.brown@example.com', '+1122334455', '789 Pine Road, Springfield, USA'),
('alice_jones', 'hashedpassword321', 'Alice', 'Jones', '1995-12-20', 'female', 'alice.jones@example.com', '+1029384756', '101 Maple Street, Springfield, USA'),
('mike_taylor', 'hashedpassword654', 'Mike', 'Taylor', '1980-06-05', 'male', 'mike.taylor@example.com', '+5647382910', '202 Birch Lane, Springfield, USA'),
('sarah_connor', 'hashedpassword987', 'Sarah', 'Connor', '1978-11-13', 'female', 'sarah.connor@example.com', '+9081726354', '303 Cedar Court, Springfield, USA'),
('kevin_lee', 'hashedpassword111', 'Kevin', 'Lee', '1992-09-22', 'male', 'kevin.lee@example.com', '+6789012345', '404 Walnut Road, Springfield, USA'),
('emma_wilson', 'hashedpassword222', 'Emma', 'Wilson', '1987-01-15', 'female', 'emma.wilson@example.com', '+3456789012', '505 Chestnut Ave, Springfield, USA'),
('chris_evans', 'hashedpassword333', 'Chris', 'Evans', '1983-04-10', 'male', 'chris.evans@example.com', '+9012345678', '606 Poplar Blvd, Springfield, USA'),
('lisa_brown', 'hashedpassword444', 'Lisa', 'Brown', '2000-07-01', 'female', 'lisa.brown@example.com', '+4567890123', '707 Redwood Drive, Springfield, USA');

-- Insert dummy data for Accounts table
INSERT INTO Accounts (Account_Name, UserID, Balance) VALUES
('John Checking', 1, 1500.50),
('Jane Savings', 2, 2500.00),
('Bob Business', 3, 500.00),
('Alice Investment', 4, 10000.00),
('Mike Retirement', 5, 3500.75),
('Sarah Personal', 6, 2000.00),
('Kevin Travel', 7, 750.00),
('Emma Education', 8, 1800.00),
('Chris Mortgage', 9, 12500.00),
('Lisa Emergency', 10, 300.00);

-- Insert dummy data for External_Transactions table
INSERT INTO External_Transactions (UserID, IBAN, Type, Amount) VALUES
(1, 1000000000000000, 'deposit', 500.00),
(2, 1000000000000001, 'withdraw', 200.00),
(3, 1000000000000002, 'deposit', 100.00),
(4, 1000000000000003, 'withdraw', 150.00),
(5, 1000000000000004, 'deposit', 250.00),
(6, 1000000000000005, 'withdraw', 300.00),
(7, 1000000000000006, 'deposit', 400.00),
(8, 1000000000000007, 'withdraw', 50.00),
(9, 1000000000000008, 'deposit', 800.00),
(10, 1000000000000009, 'withdraw', 100.00);

-- Insert dummy data for Internal_Transactions table
INSERT INTO Internal_Transactions (UserID, From_IBAN, To_IBAN, Description, Amount) VALUES
(1, 1000000000000000, 1000000000000001, 'Payment to Jane', 50.00),
(2, 1000000000000001, 1000000000000002, 'Transfer to Bob', 100.00),
(3, 1000000000000002, 1000000000000003, 'Payment to Alice', 75.00),
(4, 1000000000000003, 1000000000000004, 'Loan repayment to Mike', 200.00),
(5, 1000000000000004, 1000000000000005, 'Gift to Sarah', 300.00),
(6, 1000000000000005, 1000000000000006, 'Trip fund to Kevin', 150.00),
(7, 1000000000000006, 1000000000000007, 'Education fee for Emma', 180.00),
(8, 1000000000000007, 1000000000000008, 'Mortgage payment to Chris', 500.00),
(9, 1000000000000008, 1000000000000009, 'Emergency fund for Lisa', 250.00),
(10, 1000000000000009, 1000000000000000, 'Return from Lisa to John', 125.00);

-- Insert dummy data for Cards table
INSERT INTO Cards (CVV, UserID, IBAN, PIN) VALUES
(123, 1, 1000000000000000, 1234),
(234, 2, 1000000000000001, 2345),
(345, 3, 1000000000000002, 3456),
(456, 4, 1000000000000003, 4567),
(567, 5, 1000000000000004, 5678),
(678, 6, 1000000000000005, 6789),
(789, 7, 1000000000000006, 7890),
(890, 8, 1000000000000007, 8901),
(901, 9, 1000000000000008, 9012),
(111, 10, 1000000000000009, 1122);
