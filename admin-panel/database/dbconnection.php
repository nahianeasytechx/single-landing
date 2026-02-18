
<?php

   $servername="localhost";

   $username="root";
   $database_name="single_landing";
    $password="";

    $conn=mysqli_connect($servername,$username,$password,$database_name);
    $conn->set_charset("utf8mb4");
    if($conn->connect_error){
        die("Connection failed:" . $conn->connect_error);

    }



    ?>
