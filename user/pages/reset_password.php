<?php
// Include database connection
include('../php/db_connection.php');

$message = ''; // Variable to store the message (error/success)

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Fetch user details based on the token
    $stmt = $conn->prepare("SELECT Email, password_reset_token, password_reset_expiry FROM Users WHERE password_reset_token IS NOT NULL");
    $stmt->execute();
    $stmt->bind_result($email, $hashedToken, $expiry);
    $tokenValid = false;
    
    // Fetch all rows and check for a valid token
    while ($stmt->fetch()) {
        if (password_verify($token, $hashedToken) && strtotime($expiry) > time()) {
            // If a valid token is found
            $tokenValid = true;
            break;  // Exit the loop once a valid token is found
        }
    }

    $stmt->close();

    // Proceed only if the token is valid
    if ($tokenValid) {
        // Token is valid and not expired
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['password'])) {
            $newPassword = password_hash($_POST['password'], PASSWORD_BCRYPT);

            // Update the user's password and clear the reset token
            $updateStmt = $conn->prepare("UPDATE Users SET `Password` = ?, password_reset_token = NULL, password_reset_expiry = NULL WHERE Email = ?");
            $updateStmt->bind_param('ss', $newPassword, $email);
            if ($updateStmt->execute()) {
                $message = "<div class='success-message'>Password reset successful!</div>";
            } else {
                $message = "<div class='error-message'>Failed to reset password.</div>";
            }
            $updateStmt->close(); // Close the update statement
        }
    } else {
        $message = "<div class='error-message'>Invalid or expired token.</div>";
    }
} else {
    $message = "<div class='error-message'>No token provided.</div>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            box-sizing: border-box;
        }

        .form-container {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            box-sizing: border-box;
        }

        h1 {
            text-align: center;
            font-size: 24px;
            margin-bottom: 20px;
            color: #333;
        }

        label {
            font-size: 16px;
            color: #333;
            margin-bottom: 8px;
            display: block;
        }

        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            box-sizing: border-box;
        }

        button {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #45a049;
        }

        /* Success and error messages */
        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            font-size: 16px;
            text-align: center;
        }

        .success-message {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            font-size: 16px;
            text-align: center;
        }

        /* Responsive design for smaller screens */
        @media (max-width: 600px) {
            .form-container {
                padding: 20px;
            }

            h1 {
                font-size: 20px;
            }

            input[type="password"], button {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h1>Reset Password</h1>
        <?php echo $message; ?> <!-- Display the message -->
        <form method="POST">
            <label for="password">New Password:</label>
            <input type="password" name="password" id="password" required>
            <button type="submit">Reset Password</button>
        </form>
    </div>
</body>
</html>
