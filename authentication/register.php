<?php
// database connection
require_once '..\database\db.php';

// Check if the form was submitted via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // get form inputs
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // check if any of these fields are empty
    if (empty($username) || empty($email) || empty($password)) {
        die("Please fill in all required fields. <a href='register.html'>Go back</a>");
    }

    // validate for email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email format. <a href='register.html'>Go back</a>");
    }

    try {
        // check if username or email already exists in the database
        $checkStmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $checkStmt->execute([$username, $email]);
        
        if ($checkStmt->rowCount() > 0) {
            die("Username or Email is already taken. <a href='register.html'>Go back</a>");
        }

        // hash the password 
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // insert the new user into the 'users' table 
        $insertStmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        
        if ($insertStmt->execute([$username, $email, $hashed_password])) {
            // if success then redirect to log in page
            echo "<script>
                    alert('Registration successful! Please log in.');
                    window.location.href = 'login.html';
                  </script>";
        } else {
            echo "Something went wrong. Please try again later.";
        }

    } catch (PDOException $e) {
        // handle database errors
        die("Database Error: " . $e->getMessage());
    }

} else {
    // if someone tries to open register.php directly in the browser without submitting the form, redirect them back
    header("Location: register.html");
    exit();
}
?>