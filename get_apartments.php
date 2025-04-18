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

$search_keyword = isset($_GET['keyword']) ? mysqli_real_escape_string($connection, $_GET['keyword']) : '';
$search_field = isset($_GET['search_field']) ? $_GET['search_field'] : 'all';
$min_price = isset($_GET['min_price']) && is_numeric($_GET['min_price']) ? (float)$_GET['min_price'] : '';
$max_price = isset($_GET['max_price']) && is_numeric($_GET['max_price']) ? (float)$_GET['max_price'] : '';
$min_area = isset($_GET['min_area']) && is_numeric($_GET['min_area']) ? (float)$_GET['min_area'] : '';
$max_area = isset($_GET['max_area']) && is_numeric($_GET['max_area']) ? (float)$_GET['max_area'] : '';
$rooms = isset($_GET['rooms']) && is_numeric($_GET['rooms']) ? (int)$_GET['rooms'] : '';

$sql = "
    SELECT a.apartment_id, a.district, a.floor, a.area, a.rooms, a.price,
           o.owner_id, o.name
    FROM Apartments a
    JOIN Owners o ON a.owner_id = o.owner_id
    WHERE 1=1";

if (!empty($search_keyword)) {
    if ($search_field == 'district') {
        $sql .= " AND a.district LIKE '%$search_keyword%'";
    } elseif ($search_field == 'name') {
        $sql .= " AND o.name LIKE '%$search_keyword%'";
    } else {
        $sql .= " AND (a.district LIKE '%$search_keyword%' OR o.name LIKE '%$search_keyword%')";
    }
}

if (!empty($min_price)) {
    $sql .= " AND a.price >= $min_price";
}
if (!empty($max_price)) {
    $sql .= " AND a.price <= $max_price";
}

if (!empty($min_area)) {
    $sql .= " AND a.area >= $min_area";
}
if (!empty($max_area)) {
    $sql .= " AND a.area <= $max_area";
}

if (!empty($rooms)) {
    $sql .= " AND a.rooms = $rooms";
}

$sql .= " ORDER BY $sort_field $sort_order";

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
        <title>Квартири: пошук та сортування</title>
        <style>
            .search-panel {
                background-color: #f5f5f5;
                padding: 15px;
                margin-bottom: 20px;
                border-radius: 5px;
            }
            .search-row {
                margin-bottom: 10px;
            }
            .range-inputs {
                display: flex;
                gap: 10px;
                align-items: center;
            }
            table {
                width: 100%;
                border-collapse: collapse;
            }
            th {
                cursor: pointer;
                background-color: #f0f0f0;
            }
            th, td {
                padding: 8px;
                text-align: left;
            }
            tr:nth-child(even) {
                background-color: #f9f9f9;
            }
            .actions {
                margin-top: 20px;
            }
            button {
                padding: 8px 12px;
                cursor: pointer;
            }
        </style>
    </head>
    <body>
    <h1>Список квартир</h1>

    <div class="search-panel">
        <h3>Пошук та фільтрація</h3>
        <form action="" method="GET">
            <input type="hidden" name="sort" value="<?php echo htmlspecialchars($sort_field); ?>">
            <input type="hidden" name="order" value="<?php echo htmlspecialchars($sort_order); ?>">

            <div class="search-row">
                <label for="search_field">Шукати в полі:</label>
                <select name="search_field" id="search_field">
                    <option value="all" <?php echo $search_field == 'all' ? 'selected' : ''; ?>>Усі поля</option>
                    <option value="district" <?php echo $search_field == 'district' ? 'selected' : ''; ?>>Район</option>
                    <option value="name" <?php echo $search_field == 'name' ? 'selected' : ''; ?>>Власник</option>
                </select>
                <input type="text" name="keyword" value="<?php echo htmlspecialchars($search_keyword); ?>" placeholder="Введіть пошуковий запит">
            </div>

            <div class="search-row">
                <label>Ціна ($):</label>
                <div class="range-inputs">
                    <input type="number" name="min_price" value="<?php echo htmlspecialchars($min_price); ?>" placeholder="Від">
                    <span>—</span>
                    <input type="number" name="max_price" value="<?php echo htmlspecialchars($max_price); ?>" placeholder="До">
                </div>
            </div>

            <div class="search-row">
                <label>Площа (м²):</label>
                <div class="range-inputs">
                    <input type="number" name="min_area" value="<?php echo htmlspecialchars($min_area); ?>" placeholder="Від">
                    <span>—</span>
                    <input type="number" name="max_area" value="<?php echo htmlspecialchars($max_area); ?>" placeholder="До">
                </div>
            </div>

            <div class="search-row">
                <label for="rooms">Кількість кімнат:</label>
                <select name="rooms" id="rooms">
                    <option value="" <?php echo $rooms === '' ? 'selected' : ''; ?>>Усі</option>
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <option value="<?php echo $i; ?>" <?php echo $rooms === "$i" ? 'selected' : ''; ?>><?php echo $i; ?></option>
                    <?php endfor; ?>
                    <option value="6" <?php echo $rooms === "6" ? 'selected' : ''; ?>>6+</option>
                </select>
            </div>

            <div>
                <button type="submit">Пошук</button>
                <a href="get_apartments.php"><button type="button">Скинути фільтри</button></a>
            </div>
        </form>
    </div>

    <div>
        <strong>Сортувати за:</strong>
        <select id="sortField" onchange="updateSort()">
            <option value="apartment_id" <?php echo $sort_field == 'apartment_id' ? 'selected' : ''; ?>>ID</option>
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

    <div>
        <p>Знайдено квартир: <strong><?php echo count($apartments); ?></strong></p>
    </div>

    <table border="1">
        <thead>
        <tr>
            <th onclick="sortTable('apartment_id')">ID <?php echo sortIcon('apartment_id'); ?></th>
            <th onclick="sortTable('district')">Район <?php echo sortIcon('district'); ?></th>
            <th onclick="sortTable('floor')">Поверх <?php echo sortIcon('floor'); ?></th>
            <th onclick="sortTable('area')">Площа (м²) <?php echo sortIcon('area'); ?></th>
            <th onclick="sortTable('rooms')">Кімнати <?php echo sortIcon('rooms'); ?></th>
            <th onclick="sortTable('price')">Ціна ($) <?php echo sortIcon('price'); ?></th>
            <th onclick="sortTable('owner_id')">Власник <?php echo sortIcon('owner_id'); ?></th>
            <th>Дії</th>
        </tr>
        </thead>
        <tbody>
        <?php if (count($apartments) > 0): ?>
            <?php foreach ($apartments as $apartment): ?>
                <?php $ownerLink = "<a href='owner.php?id={$apartment['owner_id']}'>" . htmlspecialchars($apartment['name']) . "</a>"; ?>
                <tr>
                    <td><?php echo $apartment['apartment_id']; ?></td>
                    <td><?php echo htmlspecialchars($apartment['district']); ?></td>
                    <td><?php echo $apartment['floor']; ?></td>
                    <td><?php echo $apartment['area']; ?></td>
                    <td><?php echo $apartment['rooms']; ?></td>
                    <td><?php echo number_format($apartment['price'], 2); ?></td>
                    <td><?php echo $ownerLink; ?></td>
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

    <div class="actions">
        <a href="create_apartment.php"><button type="button">Додати нову квартиру</button></a>
    </div>

    <script>
        function sortTable(field) {
            const urlParams = new URLSearchParams(window.location.search);
            const currentSort = urlParams.get('sort') || 'apartment_id';
            const currentOrder = urlParams.get('order') || 'ASC';

            let newOrder = 'ASC';
            if (currentSort === field) {
                newOrder = currentOrder === 'ASC' ? 'DESC' : 'ASC';
            }

            urlParams.set('sort', field);
            urlParams.set('order', newOrder);

            window.location.href = '?' + urlParams.toString();
        }

        function updateSort() {
            const urlParams = new URLSearchParams(window.location.search);
            const field = document.getElementById('sortField').value;
            const order = document.getElementById('sortOrder').value;

            urlParams.set('sort', field);
            urlParams.set('order', order);

            window.location.href = '?' + urlParams.toString();
        }
    </script>
    </body>
    </html>

<?php
function sortIcon($field) {
    $sort_field = isset($_GET['sort']) ? $_GET['sort'] : 'apartment_id';
    $sort_order = isset($_GET['order']) ? $_GET['order'] : 'ASC';

    if ($sort_field === $field) {
        return $sort_order === 'ASC' ? '↑' : '↓';
    }
    return '';
}

mysqli_close($connection);
?>