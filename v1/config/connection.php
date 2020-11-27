<?php

$host     = 'localhost';
$user     = '';
$password = '';
$db       = '';

$conn = mysqli_connect($host, $user, $password, $db);
    if(!$conn) {
        die ("Connction Failed!");
    }
