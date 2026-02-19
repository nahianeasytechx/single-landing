
<?php

//    $servername="localhost";

//    $username="root";
//    $database_name="single_landing";
//     $password="";
    
   $servername="localhost";

   $username="techtor";
   $database_name="techytor_single_landing";
    $password="n76DXUiw:d01(S";

$conn = mysqli_connect($servername, $username, $password, $database_name);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
mysqli_set_charset($conn, "utf8mb4");




    ?>




