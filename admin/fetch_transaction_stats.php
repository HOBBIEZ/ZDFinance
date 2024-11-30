<?php
include 'db_connection.php';

$query = "
    SELECT 'Internal' AS type, COUNT(*) as count FROM Internal_Transactions
    UNION ALL
    SELECT 'External', COUNT(*) FROM External_Transactions
";
$result = mysqli_query($conn, $query);

$data = ['labels' => [], 'values' => []];
while ($row = mysqli_fetch_assoc($result)) {
    $data['labels'][] = $row['type'];
    $data['values'][] = $row['count'];
}

echo json_encode($data);
?>
