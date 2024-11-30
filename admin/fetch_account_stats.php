<?php
include 'db_connection.php';

$query = "SELECT Status, COUNT(*) as count FROM Accounts GROUP BY Status";
$result = mysqli_query($conn, $query);

$data = ['labels' => [], 'values' => []];
while ($row = mysqli_fetch_assoc($result)) {
    $data['labels'][] = ucfirst($row['Status']);
    $data['values'][] = $row['count'];
}

echo json_encode($data);
?>
