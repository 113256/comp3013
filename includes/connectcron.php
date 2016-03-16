<?php 

//mysql database variables which we connect to 
    $dbhost = 'localhost';
    $dbuser = getenv('dbuser');
    $dbpass = getenv('dbpass');
    $dbname = 'comp3013';

    
    try {
        $conn = mysqli_connect($dbhost,$dbuser,$dbpass,$dbname);
    } catch (Exception $e ) {
       echo "Failed to connect to MySQL: " . mysqli_connect_error();
    }

?>