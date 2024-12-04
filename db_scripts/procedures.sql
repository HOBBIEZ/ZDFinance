DROP PROCEDURE IF EXISTS log_user_login;
DROP PROCEDURE IF EXISTS log_user_logout;

DELIMITER $$
CREATE PROCEDURE log_user_login(IN Username VARCHAR(32))
BEGIN
    DECLARE user_id INT;
    
    -- Retrieve the UserID for the given Username
    SELECT UserID INTO user_id 
    FROM Users
    WHERE Username = Username;
    
    -- Log the user login
    INSERT INTO Audit_Logs (UserID, Type, Timestamp) 
    VALUES (user_id, 'login', CURRENT_TIMESTAMP);
END$$
DELIMITER ;



DELIMITER $$
CREATE PROCEDURE log_user_logout(IN Username VARCHAR(32))
BEGIN
    DECLARE user_id INT;
    
    -- Retrieve the UserID for the given Username
    SELECT UserID INTO user_id 
    FROM Users
    WHERE Username = Username;

    INSERT INTO Audit_Logs (UserID, Type, Timestamp) 
    VALUES (user_id, 'logout', CURRENT_TIMESTAMP);
END$$
DELIMITER ;
