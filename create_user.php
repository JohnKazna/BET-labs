<?php
$connectiontoserver = mysqli_connect("localhost", "root", "");
if($connectiontoserver) {
    echo "Connection established <br>";
} else {
    echo "Connection not established";
}
$querry = "GRANT ALL PRIVILEGES ON *.* TO 'admin'@'localhost' IDENTIFIED BY
'admin' WITH GRANT OPTION";
$user_creation = mysqli_query($connectiontoserver, $querry);
if($user_creation){
    echo "User created! <br>";
} else{
    echo "User not created :-( <br>";
}
?>