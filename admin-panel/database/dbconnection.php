
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


    function getDatabaseConnection() {
    $host = 'localhost';
    $username = 'root';
    $password = '';
    $database = 'single_landing';
    
    $conn = new mysqli($host, $username, $password, $database);
    
    if ($conn->connect_error) {
        error_log("Database connection failed: " . $conn->connect_error);
        return null;
    }
    
    return $conn;
}
    ?>
