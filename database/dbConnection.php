
<?php

   $servername="localhost";
   $username="root";
   $database_name="single_landing";
    $password="";
    
    // $host     = 'localhost';
    // $username = 'techytor';        // ← prefixed with cPanel username
    // $password = 'n76DXUiw:d01(S';
    // $database = 'techytor_single_landing';

$conn = mysqli_connect($servername, $username, $password, $database_name);
$conn->set_charset("utf8mb4");  // ← ADD THIS LINE

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
mysqli_set_charset($conn, "utf8mb4");




    ?>




