<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "apartmentsdb";

$connection = mysqli_connect($servername, $username, $password, $database);

if (!$connection) {
    die("Помилка підключення: " . mysqli_connect_error());
}

$apartment = null;
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $sql = "SELECT * FROM apartments WHERE apartment_id = $id";
    $result = mysqli_query($connection, $sql);
    $apartment = mysqli_fetch_assoc($result);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $district = mysqli_real_escape_string($connection, $_POST['district']);
    $floor = intval($_POST['floor']);
    $area = floatval($_POST['area']);
    $rooms = intval($_POST['rooms']);
    $price = floatval($_POST['price']);
    $owner_id = intval($_POST['owner_id']);

    $sql = "UPDATE Apartments SET 
            district = '$district',
            floor = $floor,
            area = $area,
            rooms = $rooms,
            price = $price,
            owner_id = $owner_id
            WHERE apartment_id = $id";

    if (mysqli_query($connection, $sql)) {
        $success_message = "Квартиру успішно оновлено!";

        $sql = "SELECT * FROM Apartments WHERE apartment_id = $id";
        $result = mysqli_query($connection, $sql);
        $apartment = mysqli_fetch_assoc($result);
    } else {
        $error_message = "Помилка при оновленні квартири: " . mysqli_error($connection);
    }
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($apartment) ? 'Редагувати квартиру' : 'Квартира не знайдена'; ?></title>
</head>
<body>
<?php if (isset($apartment)): ?>
    <h1>Редагувати квартиру #<?php echo $apartment['apartment_id']; ?></h1>

    <?php if (isset($success_message)): ?>
        <div><?php echo $success_message; ?></div>
    <?php endif; ?>

    <?php if (isset($error_message)): ?>
        <div><?php echo $error_message; ?></div>
    <?php endif; ?>

    <form method="POST">
        <input type="hidden" name="id" value="<?php echo $apartment['apartment_id']; ?>">

        <label for="district">Район:</label>
        <input type="text" id="district" name="district" value="<?php echo htmlspecialchars($apartment['district']); ?>" required>

        <label for="floor">Поверх:</label>
        <input type="number" id="floor" name="floor" min="1" max="100" value="<?php echo $apartment['floor']; ?>" required>

        <label for="area">Площа (м²):</label>
        <input type="number" id="area" name="area" step="0.01" min="10" max="1000" value="<?php echo $apartment['area']; ?>" required>

        <label for="rooms">Кількість кімнат:</label>
        <input type="number" id="rooms" name="rooms" min="1" max="20" value="<?php echo $apartment['rooms']; ?>" required>

        <label for="price">Ціна ($):</label>
        <input type="number" id="price" name="price" step="0.01" min="1000" value="<?php echo $apartment['price']; ?>" required>

        <label for="owner_id">ID власника:</label>
        <input type="number" id="owner_id" name="owner_id" min="1" value="<?php echo $apartment['owner_id']; ?>" required>

        <button type="submit">Зберегти зміни</button>
    </form>
<?php else: ?>
    <h1>Квартира не знайдена</h1>
    <div>Квартира з вказаним ID не існує.</div>
<?php endif; ?>

<br>
<a href="get_apartments.php">
    <button>Повернутися до списку квартир</button>
</a>
</body>
</html>
