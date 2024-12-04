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



DELIMITER $$
CREATE TRIGGER after_user_creation
AFTER INSERT ON Users
FOR EACH ROW
BEGIN
    INSERT INTO Audit_Logs (UserID, Type, Timestamp) VALUES (NEW.UserID, 'signup', CURRENT_TIMESTAMP);
END$$
DELIMITER ;



DELIMITER $$
CREATE TRIGGER after_account_creation
AFTER INSERT ON Accounts
FOR EACH ROW
BEGIN
    INSERT INTO Audit_Logs (UserID, Type, Timestamp) VALUES (NEW.UserID, 'acc_crt', CURRENT_TIMESTAMP);
END$$
DELIMITER ;



DELIMITER $$
CREATE TRIGGER after_account_deletion
AFTER DELETE ON Accounts
FOR EACH ROW
BEGIN
    INSERT INTO Audit_Logs (UserID, Type, Timestamp) VALUES (OLD.UserID, 'acc_del', CURRENT_TIMESTAMP);
END$$
DELIMITER ;



DELIMITER $$
CREATE TRIGGER after_card_creation
AFTER INSERT ON Cards
FOR EACH ROW
BEGIN
    INSERT INTO Audit_Logs (UserID, Type, Timestamp) VALUES (NEW.UserID, 'card_crt', CURRENT_TIMESTAMP);
END$$
DELIMITER ;



DELIMITER $$
CREATE TRIGGER after_card_deletion
AFTER DELETE ON Cards
FOR EACH ROW
BEGIN
    INSERT INTO Audit_Logs (UserID, Type, Timestamp) VALUES (OLD.UserID, 'card_del', CURRENT_TIMESTAMP);
END$$
DELIMITER ;



DELIMITER $$
CREATE TRIGGER after_internal_transaction
AFTER INSERT ON Internal_Transactions
FOR EACH ROW
BEGIN
    INSERT INTO Audit_Logs (UserID, Type, Timestamp) VALUES (NEW.UserID, 'transaction', CURRENT_TIMESTAMP);
END$$
DELIMITER ;



DELIMITER $$
CREATE TRIGGER after_external_transaction
AFTER INSERT ON External_Transactions
FOR EACH ROW
BEGIN
    INSERT INTO Audit_Logs (UserID, Type, Timestamp) VALUES (NEW.UserID, 'transaction', CURRENT_TIMESTAMP);
END$$
DELIMITER ;
