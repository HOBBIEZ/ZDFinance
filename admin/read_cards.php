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
            List of Cards
            <br><br>
            <a class="btn btn-primary" href="/ZDFinance/admin/create_card.php" role="button">New Card</a>
        </h2>
        <br>
        <table class="table">
            <thead>
                <tr>
                    <th>UserID</th>
                    <th>Card Number</th>
                    <th>CVV</th>
                    <th>IBAN</th>
                    <th>PIN</th>
                    <th>Creation Date</th>
                    <th>Expiration Date</th>
                </tr>
            </thead>
            <tbody>
                <?php

                // connect with db
                $servername = "localhost";
                $username = "root";
                $password = "";
                $database = "zdfinance";
                
                $connection = new mysqli($servername, $username, $password, $database);
                
                if ($connection->connect_error) {
                    die("Connection failed: " . $connection->connect_error);
                }

                // fetch all tables records
                $sql = "SELECT * FROM Cards";
                $result = $connection->query($sql);

                if (!$result) {
                    die("Invalid query: " . $connection->error);
                }

                // dynamically display all records of the table
                while ($row = $result->fetch_assoc()) {
                    echo "
                    <tr>
                        <td>$row[UserID]</td>
                        <td>$row[Card_Number]</td>
                        <td>$row[CVV]</td>
                        <td>$row[IBAN]</td>
                        <td>$row[PIN]</td>
                        <td>$row[Creation_Date]</td>
                        <td>$row[Expiration_Date]</td>
                        <td>
                            <a class='btn btn-primary btn-sm' href='/ZDFinance/admin/edit_card.php?Card_Number=$row[Card_Number]'>Edit</a>
                            <a class='btn btn-danger btn-sm' href='/ZDFinance/admin/delete_card.php?Card_Number=$row[Card_Number]'>Delete</a>
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
