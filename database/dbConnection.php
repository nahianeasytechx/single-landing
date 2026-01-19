
<?php

   $servername="localhost";

   $username="root";
   $database_name="single_landing";
    $password="";

$conn = mysqli_connect($servername, $username, $password, $database_name);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
mysqli_set_charset($conn, "utf8mb4");




    ?>




