<?php
// database connection using PDO

$host = 'localhost';
$dbname = 'student_registration_system';// Our database name
$username = 'root';                     // username
$password = '';                         // password  is empty

try {
    // PDO instance
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    
    // set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // set default fetch mode to associative array
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // If connection fails stop execution and display the error
    die("Database connection failed: " . $e->getMessage());
}
?>