<?php
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'apartmentsdb';

$conn = mysqli_connect($host, $user, $password, $database);

if (!$conn) {
    die("Помилка з'єднання: " . mysqli_connect_error());
}

$sql1 = "SELECT COUNT(*) as total FROM Apartments";
$result1 = mysqli_query($conn, $sql1);
$row1 = mysqli_fetch_assoc($result1);
$total_apartments = $row1['total'];

$sql2 = "SELECT COUNT(*) as total FROM Owners";
$result2 = mysqli_query($conn, $sql2);
$row2 = mysqli_fetch_assoc($result2);
$total_owners = $row2['total'];

$last_month = date('Y-m-d', strtotime('-1 month'));

$sql3a = "SELECT COUNT(*) as recent FROM Apartments WHERE created_at >= '$last_month'";
$result3a = mysqli_query($conn, $sql3a);
$row3a = mysqli_fetch_assoc($result3a);
$recent_apartments = $row3a['recent'];

$sql3b = "SELECT COUNT(*) as recent FROM Owners WHERE created_at >= '$last_month'";
$result3b = mysqli_query($conn, $sql3b);
$row3b = mysqli_fetch_assoc($result3b);
$recent_owners = $row3b['recent'];

$sql4 = "SELECT * FROM Apartments ORDER BY apartment_id DESC LIMIT 1";
$result4 = mysqli_query($conn, $sql4);
$last_apartment = mysqli_fetch_assoc($result4);

$sql5 = "SELECT o.*, COUNT(a.apartment_id) as apartment_count
        FROM Owners o
        LEFT JOIN Apartments a ON o.owner_id = a.owner_id
        GROUP BY o.owner_id
        ORDER BY apartment_count DESC
        LIMIT 1";
$result5 = mysqli_query($conn, $sql5);
$owner_with_most_apartments = mysqli_fetch_assoc($result5);
?>

    <!DOCTYPE html>
    <html lang="uk">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Статистика сайту</title>
    </head>
    <body>
    <div>
        <h1>Статистика веб-сайту</h1>

        <div>
            <div>1. Всього записів у таблиці квартир: <?php echo $total_apartments; ?></div>
            
        </div>

        <div>
            <div>2. Всього записів у таблиці власників: <?php echo $total_owners; ?></div>
        </div>

        <div>
            <div>3. Записів за останній місяць:</div>
            <div>
                У таблиці квартир: <?php echo $recent_apartments; ?><br>
                У таблиці власників: <?php echo $recent_owners; ?>
            </div>
        </div>

        <div>
            <div>4. Останній доданий запис у таблиці квартир:</div>
            <div>
                ID: <?php echo $last_apartment['apartment_id']; ?>
            </div>
            <div>
                Район: <?php echo htmlspecialchars($last_apartment['district']); ?><br>
                Поверх: <?php echo $last_apartment['floor']; ?><br>
                Площа: <?php echo $last_apartment['area']; ?> м²<br>
                Кімнат: <?php echo $last_apartment['rooms']; ?><br>
                Ціна: <?php echo number_format($last_apartment['price'], 2); ?> $
            </div>
        </div>

        <div class="stat-item">
            <div class="stat-title">5. Власник з найбільшою кількістю квартир:</div>
            <div class="record-details">
                <div class="detail-row">
                    <span class="detail-label">ID власника:</span> <?php echo $owner_with_most_apartments['owner_id']; ?>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Ім'я:</span> <?php echo htmlspecialchars($owner_with_most_apartments['name']); ?>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Телефон:</span> <?php echo $owner_with_most_apartments['phone']; ?>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Email:</span> <?php echo htmlspecialchars($owner_with_most_apartments['email']); ?>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Кількість квартир:</span> <?php echo $owner_with_most_apartments['apartment_count']; ?>
                </div>
            </div>
        </div>
    </div>
    </body>
    </html>

<?php
mysqli_close($conn);
?>