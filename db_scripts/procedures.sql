DROP PROCEDURE IF EXISTS log_user_login;
DROP PROCEDURE IF EXISTS log_user_logout;

DELIMITER $$
CREATE PROCEDURE log_user_login(IN Username VARCHAR(32))
BEGIN
    INSERT INTO Audit_Logs (UserID, Type, Timestamp) VALUES (user_id, 'login', CURRENT_TIMESTAMP);
END$$
DELIMITER ;



DELIMITER $$
CREATE PROCEDURE log_user_logout(IN Username VARCHAR(32))
BEGIN
    INSERT INTO Audit_Logs (UserID, Type, Timestamp) VALUES (user_id, 'logout', CURRENT_TIMESTAMP);
END$$
DELIMITER ;
