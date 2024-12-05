<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Webpage</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        .header {
            background-color: #007bff;
            color: white;
            padding: 15px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .header h1, .header h3 {
            margin: 0;
        }
        .header h1 a {
            color: white;
            text-decoration: none;
        }
        .btn-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 15px;
            margin-top: 30px;
        }
        .btn-container a {
            width: 200px;
        }
        .chart-container {
            margin-top: 50px;
        }
        canvas {
            max-width: 100%;
            height: 400px;
            margin: auto;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1><a href="index.html">ZDFinance</a></h1>
        <h3>Admin WebPage (God mode)</h3>
    </div>
    <div class="container my-5">
        <h2>
            List of Clients
            <a class="btn btn-primary" href="/ZDFinance/admin/create_client.php" role="button">New Client</a>
        </h2>
        <br>
        <table class="table">
            <thead>
                <tr>
                    <th>UserID</th>
                    <th>Username</th>
                    <th>Creation Date</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>DoB</th>
                    <th>Gender</th>
                    <th>Email</th>
                    <th>Phone Number</th>
                    <th>Address</th>
                    <th>Action</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php

                // connect with db
                $servername = "localhost";
                $username = "root";
                $password = "";
                $database = "pos_db";
                
                $connection = new mysqli($servername, $username, $password, $database);
                
                if ($connection->connect_error) {
                    die("Connection failed: " . $connection->connect_error);
                }

                // fetch all tables records
                $sql = "SELECT * FROM Users";
                $result = $connection->query($sql);

                if (!$result) {
                    die("Invalid query: " . $connection->error);
                }

                // dynamically display all records of the table
                while ($row = $result->fetch_assoc()) {
                    echo "
                    <tr>
                        <td>$row[UserID]</td>
                        <td>$row[Username]</td>
                        <td>$row[Creation_Date]</td>
                        <td>$row[First_Name]</td>
                        <td>$row[Last_Name]</td>
                        <td>$row[Date_of_Birth]</td>
                        <td>$row[Gender]</td>
                        <td>$row[Email]</td>
                        <td>$row[Phone_Number]</td>
                        <td>$row[Address]</td>
                        <td>$row[Status]</td>
                        <td>
                            <a class='btn btn-primary btn-sm' href='/ZDFinance/admin/edit_client.php?UserID=$row[UserID]'>Edit</a>
                            <a class='btn btn-danger btn-sm' href='/ZDFinance/admin/delete_client.php?UserID=$row[UserID]'>Delete</a>
                        </td>
                    </tr>
                    ";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
