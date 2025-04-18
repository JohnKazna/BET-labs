<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "apartmentsdb";

$connection = mysqli_connect($servername, $username, $password, $database);

if (!$connection) {
    die("Помилка підключення: " . mysqli_connect_error());
}

$success_message = '';
$error_message = '';
$apartment = null;

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $sql = "SELECT * FROM Apartments WHERE apartment_id = $id";
    $result = mysqli_query($connection, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $apartment = mysqli_fetch_assoc($result);
    } else {
        $error_message = "Квартира з ID $id не знайдена.";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);

    $sql = "DELETE FROM Apartments WHERE apartment_id = $id";

    if (mysqli_query($connection, $sql)) {
        $success_message = "Квартиру успішно видалено!";
        $apartment = null;
    } else {
        $error_message = "Помилка при видаленні квартири: " . mysqli_error($connection);
    }
}
?>

    <!DOCTYPE html>
    <html lang="uk">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Видалити квартиру</title>
    </head>
    <body>
    <h1>Видалити квартиру</h1>

    <?php if (isset($success_message) && $success_message): ?>
        <div><?php echo $success_message; ?></div>
        <div>
            <a href="get_apartments.php"><button>Повернутися до списку квартир</button></a>
        </div>
    <?php elseif (isset($error_message) && $error_message): ?>
        <div><?php echo $error_message; ?></div>
        <div>
            <a href="get_apartments.php"><button>Повернутися до списку квартир</button></a>
        </div>
    <?php elseif ($apartment): ?>
        <div>
            <h2>Підтвердження видалення</h2>
            <p>Ви дійсно хочете видалити цю квартиру?</p>

            <div>
                <strong>ID:</strong> <?php echo $apartment['apartment_id']; ?>
            </div>
            <div>
                <strong>Район:</strong> <?php echo htmlspecialchars($apartment['district']); ?>
            </div>
            <div>
                <strong>Поверх:</strong> <?php echo $apartment['floor']; ?>
            </div>
            <div>
                <strong>Площа:</strong> <?php echo $apartment['area']; ?> м²
            </div>
            <div">
                <strong>Кімнат:</strong> <?php echo $apartment['rooms']; ?>
            </div>
            <div>
                <strong>Ціна:</strong> $<?php echo number_format($apartment['price'], 2); ?>
            </div>

            <form method="POST">
                <input type="hidden" name="id" value="<?php echo $apartment['apartment_id']; ?>">
                <div>
                    <button type="submit">Так, видалити</button>
                    <a href="get_apartments.php"><button type="button">Скасувати</button></a>
                </div>
            </form>
        </div>
    <?php else: ?>
        <div>Квартира не знайдена.</div>
        <div>
            <a href="get_apartments.php"><button>Повернутися до списку квартир</button></a>
        </div>
    <?php endif; ?>
    </body>
    </html>

<?php
mysqli_close($connection);
?>