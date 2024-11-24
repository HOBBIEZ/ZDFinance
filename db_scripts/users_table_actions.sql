-- USER DEFINED ACTIONS --

INSERT INTO Users (Username, Password, First_Name, Last_Name, Date_of_Birth, Gender, Email, Phone_Number, Address)
VALUES (:username, :hashed_password, :first_name, :last_name, :date_of_birth, :gender, :email, :phone_number, :address);


DELETE FROM Users 
WHERE UserID = :user_id;


-- TRIGGER TO DELETE ALL ACCOUNTS UNDER THIS USER --


UPDATE Users 
SET Address = :new_address 
WHERE UserID = :user_id;


SELECT Usename, Password, First_Name, Last_Name, Date_of_Birth, Gender, Email, Phone_Number, Address
FROM Users 
WHERE UserID = :user_id;

-- INFORM THE USER THAT HE CANNOT DELETE ALL OF HIS ACCOUNTS. INSTEAD DELETE YOUR USER -- xoxo


---------------------------------------------------------



-- ADMIN ACTIONS --


SELECT *
FROM Users


UPDATE Users 
SET 
    Email = :new_email, 
    Phone_Number = :new_phone_number 
WHERE UserID = :user_id;


DELETE FROM Users 
WHERE UserID = :user_id;


-- TRIGGER TO DELETE ALL ACCOUNTS UNDER THESE USERS --


DELETE FROM Users 
WHERE Date_of_Birth < :cutoff_date;
