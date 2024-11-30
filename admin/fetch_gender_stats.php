<?php
include 'db_connection.php';

$query = "SELECT Gender, COUNT(*) as count FROM Users GROUP BY Gender";
$result = mysqli_query($conn, $query);

$data = ['labels' => [], 'values' => []];
while ($row = mysqli_fetch_assoc($result)) {
    $data['labels'][] = ucfirst($row['Gender']);
    $data['values'][] = $row['count'];
}

echo json_encode($data);
?>
