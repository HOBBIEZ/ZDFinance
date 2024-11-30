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

INSERT INTO External_Transactions (TransactionID, UserID, Type, Amount, Timestamp, Status, External_Account)
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

INSERT INTO Audit_Logs (Log_Id, UserID, Type, Timestamp, Status)
VALUES
(1, 1, 'Login', CURRENT_TIMESTAMP, 'Success'),
(2, 2, 'Transaction', CURRENT_TIMESTAMP, 'Success'),
(3, 3, 'Login', CURRENT_TIMESTAMP, 'Failure'),
(4, 4, 'Card Issued', CURRENT_TIMESTAMP, 'Success'),
(5, 5, 'Account Update', CURRENT_TIMESTAMP, 'Warning');
