
-- USER DEFINED ACTIONS --

INSERT INTO Accounts (UserID, Transfer_Limit, Balance, Status)
VALUES (:user_id, :transfer_limit, :balance, :status);


DELETE FROM Accounts
WHERE IBAN = :user_iban;


UPDATE Accounts
SET Balance = :new_balance
WHERE IBAN = :user_iban;


SELECT * 
FROM Accounts
WHERE IBAN = :iban;


------------------------------


-- ADMIN DEFINED ACTIONS --

SELECT * 
FROM Accounts;


UPDATE Accounts
SET Status = :new_status,
    Balance = :new_balance
WHERE IBAN = :iban;


DELETE FROM Accounts
WHERE Status = 'inactive';


