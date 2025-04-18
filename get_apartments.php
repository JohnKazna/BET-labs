<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "apartmentsdb";

$connection = mysqli_connect($servername, $username, $password, $database);

if (!$connection) {
    die("Помилка підключення: " . mysqli_connect_error());
}

$sort_field = isset($_GET['sort']) ? $_GET['sort'] : 'apartment_id';
$sort_order = isset($_GET['order']) ? $_GET['order'] : 'ASC';

$allowed_fields = ['apartment_id', 'district', 'floor', 'area', 'rooms', 'price', 'owner_id'];
if (!in_array($sort_field, $allowed_fields)) {
    $sort_field = 'apartment_id';
}

$allowed_orders = ['ASC', 'DESC'];
if (!in_array(strtoupper($sort_order), $allowed_orders)) {
    $sort_order = 'ASC';
}

$sql = "SELECT * FROM Apartments ORDER BY $sort_field $sort_order";
$result = mysqli_query($connection, $sql);

$apartments = [];
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $apartments[] = $row;
    }
}
?>

    <!DOCTYPE html>
    <html lang="uk">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Сортування квартир</title>
    </head>
    <body>
    <h1>Список квартир</h1>

    <div>
        <strong>Сортувати за:</strong>
        <select id="sortField" onchange="updateSort()">
            <option value="id" <?php echo $sort_field == 'apartment_id' ? 'selected' : ''; ?>>ID</option>
            <option value="district" <?php echo $sort_field == 'district' ? 'selected' : ''; ?>>Район</option>
            <option value="floor" <?php echo $sort_field == 'floor' ? 'selected' : ''; ?>>Поверх</option>
            <option value="area" <?php echo $sort_field == 'area' ? 'selected' : ''; ?>>Площа</option>
            <option value="rooms" <?php echo $sort_field == 'rooms' ? 'selected' : ''; ?>>Кімнати</option>
            <option value="price" <?php echo $sort_field == 'price' ? 'selected' : ''; ?>>Ціна</option>
            <option value="owner_id" <?php echo $sort_field == 'owner_id' ? 'selected' : ''; ?>>ID власника</option>
        </select>

        <select id="sortOrder" onchange="updateSort()">
            <option value="ASC" <?php echo $sort_order == 'ASC' ? 'selected' : ''; ?>>За зростанням (А-Я)</option>
            <option value="DESC" <?php echo $sort_order == 'DESC' ? 'selected' : ''; ?>>За спаданням (Я-А)</option>
        </select>
    </div>

    <table border='1'>
        <thead>
        <tr>
            <th onclick="sortTable('id')">ID <?php echo sortIcon('id'); ?></th>
            <th onclick="sortTable('district')">Район <?php echo sortIcon('district'); ?></th>
            <th onclick="sortTable('floor')">Поверх <?php echo sortIcon('floor'); ?></th>
            <th onclick="sortTable('area')">Площа (м²) <?php echo sortIcon('area'); ?></th>
            <th onclick="sortTable('rooms')">Кімнати <?php echo sortIcon('rooms'); ?></th>
            <th onclick="sortTable('price')">Ціна ($) <?php echo sortIcon('price'); ?></th>
            <th onclick="sortTable('owner_id')">ID власника <?php echo sortIcon('owner_id'); ?></th>
            <th>Дії</th>
        </tr>
        </thead>
        <tbody>
        <?php if (count($apartments) > 0): ?>
            <?php foreach ($apartments as $apartment): ?>
                <tr>
                    <td><?php echo $apartment['apartment_id']; ?></td>
                    <td><?php echo htmlspecialchars($apartment['district']); ?></td>
                    <td><?php echo $apartment['floor']; ?></td>
                    <td><?php echo $apartment['area']; ?></td>
                    <td><?php echo $apartment['rooms']; ?></td>
                    <td><?php echo number_format($apartment['price'], 2); ?></td>
                    <td><?php echo $apartment['owner_id']; ?></td>
                    <td>
                        <a href="edit_apartment.php?id=<?php echo $apartment['apartment_id']; ?>">Редагувати</a>
                        <a href="delete_apartment.php?id=<?php echo $apartment['apartment_id']; ?>">Видалити</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="8" style="text-align: center;">Немає даних про квартири</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>

    <div>
        <a href="create_apartment.php"><button type="submit">Додати нову квартиру</button></a>
    </div>

    <script>
        function sortTable(field) {
            const currentSort = '<?php echo $sort_field; ?>';
            const currentOrder = '<?php echo $sort_order; ?>';

            let newOrder = 'ASC';
            if (currentSort === field) {
                newOrder = currentOrder === 'ASC' ? 'DESC' : 'ASC';
            }

            window.location.href = `?sort=${field}&order=${newOrder}`;
        }

        function updateSort() {
            const field = document.getElementById('sortField').value;
            const order = document.getElementById('sortOrder').value;
            window.location.href = `?sort=${field}&order=${order}`;
        }
    </script>
    </body>
    </html>

<?php

function sortIcon($field) {
    $sort_field = isset($_GET['sort']) ? $_GET['sort'] : 'id';
    $sort_order = isset($_GET['order']) ? $_GET['order'] : 'ASC';

    if ($sort_field === $field) {
        return $sort_order === 'ASC' ? '↑' : '↓';
    }
    return '';
}

mysqli_close($connection);
?>