<?php
$owner_id = $_GET['id'] ?? 0;
$owner_id = (int)$owner_id; 

$conn = mysqli_connect('localhost', 'root', '', 'apartmentsdb');

$sql = "SELECT * FROM Owners WHERE owner_id = $owner_id";
$result = mysqli_query($conn, $sql);

if ($row = mysqli_fetch_assoc($result)) {
    echo "<h2>Власник: {$row['name']}</h2>";
    echo "<p>Телефон: {$row['phone']}</p>";
    echo "<p>Email: {$row['email']}</p>";
} else {
    echo "Власника не знайдено.";
}

mysqli_close($conn);
?>
