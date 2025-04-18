<body>
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
$link = mysqli_connect("localhost", "admin", "admin");
$db = "apartmentsdb";
$select = mysqli_select_db($link, $db);
if ($select) {
    echo "Database selected <br>";
} else {
    echo "Database not selected <br>";
}
$querry = "CREATE TABLE Apartments (
    apartment_id INT PRIMARY KEY AUTO_INCREMENT,
    district VARCHAR(100),
    floor INT,
    area DECIMAL(5, 2),
    rooms INT,
    owner_id INT,
    price DECIMAL(10, 2)
);";
$table_creation = mysqli_query($link, $querry);
if($table_creation){
    echo "Table created! <br>";
} else{
    echo "Table not created" . mysqli_error($link) . "<br>";
}
?>
</body>