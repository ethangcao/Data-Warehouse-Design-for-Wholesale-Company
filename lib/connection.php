<?php
    $servername = 'localhost:3306';
    $username = 'test';
    $password = 'test1234';
    $dbname = 'team042';

    #Create connection
    $db = new mysqli($servername, $username, $password, $dbname);

    #Check connection

    if($db->connect_error){
        die("Connection Failed: ".$db->connect_error);

    }

    //echo "Connected successfully";

?>
