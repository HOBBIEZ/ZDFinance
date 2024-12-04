DROP TRIGGER IF EXISTS before_insert_cards;
DROP TRIGGER IF EXISTS check_age_before_insert;
DROP TRIGGER IF EXISTS after_user_creation;
DROP TRIGGER IF EXISTS after_user_deletion;
DROP TRIGGER IF EXISTS after_account_creation;
DROP TRIGGER IF EXISTS after_account_deletion;
DROP TRIGGER IF EXISTS after_card_creation;
DROP TRIGGER IF EXISTS after_card_deletion;
DROP TRIGGER IF EXISTS after_internal_transaction;
DROP TRIGGER IF EXISTS after_external_transaction;

DELIMITER $$
CREATE TRIGGER before_insert_cards
BEFORE INSERT ON Cards
FOR EACH ROW
BEGIN
    DECLARE next_cvv INT DEFAULT 0;
    IF (SELECT COUNT(*) FROM Cards) > 0 THEN
        SELECT IFNULL(CVV, -1) + 1 INTO next_cvv 
        FROM Cards
        ORDER BY Creation_Date DESC
        LIMIT 1;
    END IF;
    IF next_cvv > 999 THEN
        SET next_cvv = 0;
    END IF;
    SET NEW.CVV = next_cvv;
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
AFTER UPDATE ON Users
FOR EACH ROW
BEGIN
    IF OLD.Status != NEW.Status AND NEW.Status = 'inactive' THEN
        INSERT INTO Audit_Logs (UserID, Type, Timestamp)
        VALUES (OLD.UserID, 'inactive', CURRENT_TIMESTAMP);
    END IF;
END$$



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
    UPDATE Accounts
    SET Balance = Balance + NEW.Amount
    WHERE IBAN = NEW.To_IBAN;
    UPDATE Accounts
    SET Balance = Balance - NEW.Amount
    WHERE IBAN = NEW.From_IBAN;
    INSERT INTO Audit_Logs (UserID, Type, Timestamp) VALUES (NEW.UserID, 'transaction', CURRENT_TIMESTAMP);
END$$
DELIMITER ;



DELIMITER $$
CREATE TRIGGER after_external_transaction
AFTER INSERT ON External_Transactions
FOR EACH ROW
BEGIN
    IF NEW.Type = 'deposit' THEN
        UPDATE Accounts
        SET Balance = Balance + NEW.Amount
        WHERE IBAN = NEW.IBAN;
    ELSEIF NEW.Type = 'withdraw' THEN
        UPDATE Accounts
        SET Balance = Balance - NEW.Amount
        WHERE IBAN = NEW.IBAN;
    END IF;
    INSERT INTO Audit_Logs (UserID, Type, Timestamp) VALUES (NEW.UserID, 'transaction', CURRENT_TIMESTAMP);
END$$
DELIMITER ;
