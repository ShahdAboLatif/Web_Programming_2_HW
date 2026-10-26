<?php
// database connection using PDO

$host = 'sql105.infinityfree.com';
$dbname = 'if0_43276672_student_registration_system_db';
$username = 'root';                     
$password = 'if0_43276672';                         

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