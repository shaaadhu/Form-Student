<?php

$servername = "localhost";
$username = "root";
$password = "9483"; 
$dbName = "student_form";
$message = '';
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbName", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);   
} catch (PDOException $e) {
    $message = "<p class='alert alert-danger mt-3'> Database Error: " . $e->getMessage() . "</p>";
}

?>