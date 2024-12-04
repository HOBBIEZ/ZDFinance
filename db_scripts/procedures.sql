DROP PROCEDURE IF EXISTS log_user_login;
DROP PROCEDURE IF EXISTS log_user_logout;

DELIMITER $$
CREATE PROCEDURE log_user_login(IN UsernameParam VARCHAR(32))
BEGIN
    DECLARE user_id INT;
    
    SELECT UserID INTO user_id 
    FROM Users
    WHERE Username = UsernameParam;
    
    INSERT INTO Audit_Logs (UserID, Type, Timestamp) 
    VALUES (user_id, 'login', CURRENT_TIMESTAMP);
END$$
DELIMITER ;



DELIMITER $$ 
CREATE PROCEDURE log_user_logout(IN UsernameParam VARCHAR(32))
BEGIN
    DECLARE user_id INT;
    
    -- Use the parameter with a different name to avoid confusion
    SELECT UserID INTO user_id
    FROM Users
    WHERE Username = UsernameParam;

    -- Insert logout log
    INSERT INTO Audit_Logs (UserID, Type, Timestamp)
    VALUES (user_id, 'logout', CURRENT_TIMESTAMP);
END$$ 
DELIMITER ;
