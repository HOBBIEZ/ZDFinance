<?php

include('db_connection.php');
require '../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function checkSession() {
    global $conn;
    if (isset($_SESSION['user'])) {
        $username = $_SESSION['user'];
        $query = "SELECT COUNT(*) FROM Users WHERE Username = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->bind_result($userExists);
        $stmt->fetch();

        if ($userExists == 0) {
            session_unset(); // Clear the session
            session_destroy(); // Destroy the session
            return ['success' => true, 'loggedIn' => false];
        }
        return ['success' => true, 'loggedIn' => true, 'user' => $_SESSION['user']];
    }
    return ['success' => true, 'loggedIn' => false];
}

function loginUser($username, $password) {
    global $conn;
    $stmt = $conn->prepare("SELECT Password FROM Users WHERE Username = ?");
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($hashed_password);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            $stmt = $conn->prepare("CALL log_user_login(?)");
            $stmt->bind_param('s', $username);
            $stmt->execute();
            $stmt = $conn->prepare("SELECT Status FROM Users WHERE Username = ?");
            $stmt->bind_param('s', $username);
            $stmt->execute();
            $stmt->store_result();
            $stmt->bind_result($status);
            $stmt->fetch();
            if ($status === 'active') {
                $response = ['success' => true];
            } else {
                $response = ['success' => false, 'error' => 'Invalid username or password.'];
            }            
        } else {
            $response = ['success' => false, 'error' => 'Invalid username or password.'];
        }
    } else {
        $response = ['success' => false, 'error' => 'Invalid username or password.'];
    }

    $stmt->close();
    return $response;
}

function forgotPassword($email) {
    global $conn;
    $stmt = $conn->prepare("SELECT Username FROM Users WHERE Email = ?");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($username);
        $stmt->fetch();

        // Generate a unique reset token
        $token = bin2hex(random_bytes(32)); // 64-character token
        $hashedToken = password_hash($token, PASSWORD_DEFAULT);
        $expiry = date('Y-m-d H:i:s', strtotime('+1 hour')); // Token expires in 1 hour
        // Save token and expiry to database
        $updateStmt = $conn->prepare("UPDATE Users SET password_reset_token = ?, password_reset_expiry = ? WHERE Email = ?");
        $updateStmt->bind_param('sss', $hashedToken, $expiry, $email);
        $updateStmt->execute();

        // Prepare reset password link
        // Execute ipconfig command to get network details
        exec('ipconfig', $output);

        // Initialize variables to track when we're in the 'Wireless LAN adapter Wi-Fi' section
        $wifiSectionFound = false;
        $ipv4Address = '';

        foreach ($output as $line) {
            // Look for the "Wireless LAN adapter Wi-Fi" section
            if (strpos($line, 'Wireless LAN adapter Wi-Fi') !== false) {
                $wifiSectionFound = true;
            }
            
            // Once we're in the Wi-Fi section, look for the IPv4 address
            if ($wifiSectionFound && strpos($line, 'IPv4 Address') !== false) {
                // Extract the IPv4 address using regular expression
                preg_match('/:\s*(\d+\.\d+\.\d+\.\d+)/', $line, $matches);
                if (isset($matches[1])) {
                    $ipv4Address = $matches[1];
                    break;  // Found the IPv4 address, no need to continue searching
                }
            }
        }

        $resetLink = "http://$ipv4Address/ZDFinance/user/pages/reset_password.php?token=$token";
        $message = "Dear $username,\n\nFollow this link to reset your password:\n$resetLink\n\nThis link will expire in 1 hour.";

        $subject = "Reset password";
        if (sendMail($email, $username, $message, $subject)) {
            $response = ['success' => true];
        } else {
            $response = ['success' => false, 'error' => 'Failed to send email.'];
        }
    } else {
        $response = ['success' => false, 'error' => 'No user with this email.'];
    }

    $stmt->close();
    return $response;
}

function sendMail($receiver, $username, $message, $subject) {
    $mail = new PHPMailer(true);

    try {
        // Step 3: Server settings
        $mail->isSMTP();  // Set mailer to use SMTP
        $mail->Host = 'smtp.gmail.com';  // Gmail SMTP server
        $mail->SMTPAuth = true;  // Enable SMTP authentication
        $mail->Username = '';  // Your Gmail address
        $mail->Password = '';  // Your Gmail App Password (use App Password if 2FA is enabled)
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;  // Enable TLS encryption
        $mail->Port = 587;  // Gmail's SMTP port
    
        // Step 4: Set email recipients
        $mail->setFrom('email', 'name');  // Sender's email and name
        $mail->addAddress($receiver, $username);  // Recipient's email and name
    
        // Step 5: Set email content
        $mail->isHTML(true);  // Set email format to HTML
        $mail->Subject = $subject;  // Email subject
        $mail->Body    = $message;  // HTML message body
        $mail->AltBody = $message;  // Plain text message body
    
        // Step 6: Send email
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Mailer Error: {$mail->ErrorInfo}"); // Log the error for debugging
        return false;
    }
}

function signupUser($username, $password, $first_name, $last_name, $dob, $gender, $email, $phone, $address) {
    global $conn;

    $dobDate = new DateTime($dob);
    $currentDate = new DateTime();
    // Ensure the date of birth is not in the future
    if ($dobDate > $currentDate) {
        return ['success' => false, 'error' => 'You must be between 18 and 99 years old to sign up.'];
    }

    // Calculate age
    $dateInterval = $currentDate->diff($dobDate);
    $age = $dateInterval->y;

    // Validate age range
    if ($age < 18 || $age > 99) {
        return ['success' => false, 'error' => 'You must be between 18 and 99 years old to sign up.'];
    }
    
    // Check for duplicate email
    $stmt = $conn->prepare("SELECT * FROM Users WHERE Email = ?");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $stmt->close();
        return ['success' => false, 'error' => 'An account with this email already exists.'];
    }
    
    // Check for duplicate phone
    $stmt = $conn->prepare("SELECT * FROM Users WHERE Phone_Number = ?");
    $stmt->bind_param('s', $phone);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $stmt->close();
        return ['success' => false, 'error' => 'An account with this phone number already exists. Please use a different one.'];
    }

    // Check for duplicate username
    $stmt = $conn->prepare("SELECT * FROM Users WHERE Username = ?");
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $stmt->close();
        return ['success' => false, 'error' => 'Username is already taken. Please choose a different username.'];
    }

    // Insert the user into the database
    $stmt = $conn->prepare("INSERT INTO Users (Username, Password, First_Name, Last_Name, Date_of_Birth, Gender, Email, Phone_Number, Address) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('sssssssss', $username, $password, $first_name, $last_name, $dob, $gender, $email, $phone, $address);
    if ($stmt->execute()) {
        $response = ['success' => true];
    } else {
        $response = ['success' => false, 'error' => 'Error signing up. Please try again.'];
    }

    $stmt->close();
    return $response;
}

function updateUser($current_username, $username, $first_name, $last_name, $email, $phone, $address) {
    global $conn;

    // Check for duplicate username
    $stmt = $conn->prepare("SELECT * FROM Users WHERE Username = ?");
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    if ($result->num_rows > 0 && $row['Username'] !== $current_username) {
        $stmt->close();
        return ['success' => false, 'error' => 'Username is already taken.'];
    }

    // Check for duplicate email
    $stmt = $conn->prepare("SELECT * FROM Users WHERE Email = ?");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    if ($result->num_rows > 0 && $row['Username'] !== $current_username) {
        $stmt->close();
        return ['success' => false, 'error' => 'An account with this email already exists.'];
    }
    
    // Check for duplicate phone
    $stmt = $conn->prepare("SELECT * FROM Users WHERE Phone_Number = ?");
    $stmt->bind_param('s', $phone);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    if ($result->num_rows > 0 && $row['Username'] !== $current_username) {
        $stmt->close();
        return ['success' => false, 'error' => 'An account with this phone number already exists.'];
    }

    // Proceed with updating the user's information
    $query = "UPDATE Users SET Username = ?, First_Name = ?, Last_Name = ?, Email = ?, Phone_Number = ?, Address = ? WHERE Username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssssss", $username, $first_name, $last_name, $email, $phone, $address, $current_username);
    // Execute the query and check if it was successful
    if ($stmt->execute()) {
        $response = ['success' => true];
    } else {
        $response = ['success' => false, 'error' => 'Error updating profile. Please try again.'];
    }

    $stmt->close();
    return $response;
}

function openAccount($account_name) {
    global $conn;
    $username = $_SESSION['user'];

    // Prepare SQL statement to fetch the UserID
    $query = "SELECT UserID FROM Users WHERE Username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $UserID = $result->fetch_assoc()['UserID'];

    // Insert new account
    $stmt = $conn->prepare("INSERT INTO Accounts (Account_Name, UserID) VALUES (?, ?)");
    $stmt->bind_param('si', $account_name, $UserID);
    if ($stmt->execute()) {
        $response = ['success' => true];
    } else {
        $response = ['success' => false, 'error' => 'Error creating account. Please try again.'];
    }

    $stmt->close();
    return $response;
}
?>
