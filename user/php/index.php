<?php
include('functions.php');

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];

    if ($action === 'checkSession') { // Check session status
        $result = checkSession();
        echo json_encode($result);
        exit;
    } elseif ($action === 'login') { // handle login
        $username = $_POST['username'];
        $password = $_POST['password'];

        $result = loginUser($username, $password);
        if ($result['success']) {
            $_SESSION['user'] = $username; // Set session if login succeeds
        }
        echo json_encode($result);
        exit;
    } elseif ($action === 'forgot') { // handle forgot password
        $email = $_POST['email'];

        $result = forgotPassword($email);
        echo json_encode($result);
        exit;
    } elseif ($action === 'signup') { // handle signup
        $username = $_POST['username'];
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $dob = $_POST['dob'];
        $gender = $_POST['gender'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        $address = $_POST['address'];

        $result = signupUser($username, $password, $first_name, $last_name, $dob, $gender, $email, $phone, $address);
        if ($result['success']) {
            $_SESSION['user'] = $username; // Set session if login succeeds
        }
        echo json_encode($result);
        exit;
    } elseif ($action === 'update_user') { // handle update user
        $current_username = $_SESSION['user'];
        $username = $_POST['username'];
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        $address = $_POST['address'];

        $result = updateUser($current_username, $username, $first_name, $last_name, $email, $phone, $address);
        if ($result['success']) {
            $_SESSION['user'] = $username; // Set session if login succeeds
        }
        echo json_encode($result);
        exit;
    } elseif ($action === 'account') { // handle open account
        $account_name = $_POST['account_name'];

        $result = openAccount($account_name);
        echo json_encode($result);
        exit;
    }
}
?>
