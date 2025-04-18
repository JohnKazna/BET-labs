<?php
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'apartmentsdb';

$conn = mysqli_connect($host, $user, $password, $database);

if (!$conn) {
    die("Помилка з'єднання: " . mysqli_connect_error());
}

$sql = "
    SELECT a.apartment_id, a.district, a.floor, a.area, a.rooms, a.price,
           o.owner_id, o.name AS owner_name, o.phone, o.email
    FROM Apartments a
    JOIN Owners o ON a.owner_id = o.owner_id
";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    echo "<table border='1'>
            <tr>
                <th>№</th><th>Район</th><th>Поверх</th><th>Площа</th><th>Кімнат</th><th>Ціна</th>
                <th>Власник</th><th>Телефон</th><th>Email</th>
            </tr>";
    while($row = mysqli_fetch_assoc($result)) {
        $ownerLink = "<a href='owner.php?id={$row['owner_id']}'>" . htmlspecialchars($row['owner_name']) . "</a>";
        echo "<tr>
                <td>{$row['apartment_id']}</td>
                <td>{$row['district']}</td>
                <td>{$row['floor']}</td>
                <td>{$row['area']}</td>
                <td>{$row['rooms']}</td>
                <td>{$row['price']}</td>
                <td>$ownerLink</td>
                <td>{$row['phone']}</td>
                <td>{$row['email']}</td>
              </tr>";
    }
    echo "</table>";
} else {
    echo "Немає даних";
}

mysqli_close($conn);
?>
