<?php
// Include the database connection file
require_once 'db.php';

// Check if the form was submitted via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Retrieve and sanitize form inputs
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // 1. Basic Validation: Check if fields are empty
    if (empty($username) || empty($email) || empty($password)) {
        die("Please fill in all required fields. <a href='register.html'>Go back</a>");
    }

    // 2. Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email format. <a href='register.html'>Go back</a>");
    }

    try {
        // 3. Check if username or email already exists in the database
        $checkStmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $checkStmt->execute([$username, $email]);
        
        if ($checkStmt->rowCount() > 0) {
            die("Username or Email is already taken. <a href='register.html'>Go back</a>");
        }

        // 4. Hash the password securely using password_hash()
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // 5. Insert the new user into the 'users' table using a prepared statement
        $insertStmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        
        if ($insertStmt->execute([$username, $email, $hashed_password])) {
            // Registration successful! Redirect user to the login page with a JavaScript alert
            echo "<script>
                    alert('Registration successful! Please log in.');
                    window.location.href = 'login.html';
                  </script>";
        } else {
            echo "Something went wrong. Please try again later.";
        }

    } catch (PDOException $e) {
        // Handle database errors safely
        die("Database Error: " . $e->getMessage());
    }

} else {
    // If someone tries to open register.php directly in the browser without submitting the form, redirect them back
    header("Location: register.html");
    exit();
}
?>