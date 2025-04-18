<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "apartmentsdb";

$connection = mysqli_connect($servername, $username, $password, $database);

if (!$connection) {
    die("Помилка підключення: " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $district = mysqli_real_escape_string($connection, $_POST['district']);
    $floor = intval($_POST['floor']);
    $area = floatval($_POST['area']);
    $rooms = intval($_POST['rooms']);
    $price = floatval($_POST['price']);
    $owner_id = intval($_POST['owner_id']);

    $sql = "INSERT INTO Apartments (district, floor, area, rooms, price, owner_id)
            VALUES ('$district', $floor, '$area', '$rooms', '$price', '$owner_id')";

    if (mysqli_query($connection, $sql)) {
        echo "Квартиру успішно додано!";
    } else {
         echo "Помилка при додаванні квартири: " . mysqli_error($connection);
    }
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Додати квартиру</title>
</head>
<body>
<h1>Додати нову квартиру</h1>

<?php if (isset($success_message)): ?>
    <div><?php echo $success_message; ?></div>
<?php endif; ?>

<?php if (isset($error_message)): ?>
    <div><?php echo $error_message; ?></div>
<?php endif; ?>

<form method="POST">
    <label for="district">Район:</label>
    <input type="text" id="district" name="district" required>

    <label for="floor">Поверх:</label>
    <input type="number" id="floor" name="floor" min="1" max="100" required>

    <label for="area">Площа (м²):</label>
    <input type="number" id="area" name="area" step="0.01" min="10" max="1000" required>

    <label for="rooms">Кількість кімнат:</label>
    <input type="number" id="rooms" name="rooms" min="1" max="20" required>

    <label for="price">Ціна ($):</label>
    <input type="number" id="price" name="price" step="0.01" min="1000" required>

    <label for="owner_id">ID власника:</label>
    <input type="number" id="owner_id" name="owner_id" min="1" required>

    <button type="submit">Додати квартиру</button>
</form>

<br>
<a href="get_apartments.php" style="text-decoration: none;">
    <button style="width: auto; display: inline-block;">Перейти до списку квартир</button>
</a>
</body>
</html>
