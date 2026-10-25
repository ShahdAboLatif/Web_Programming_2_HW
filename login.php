<?php
// Include database connection
require_once 'db.php';

// Start session
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve inputs and sanitize/trim
    $username_input = trim($_POST['username']);
    $password_input = $_POST['password'];

    if (empty($username_input) || empty($password_input)) {
        die("Please fill in all fields. <a href='login.html'>Go back</a>");
    }

    try {
        // Find user by username or email
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username_input, $username_input]);
        $user = $stmt->fetch();

        // Verify if user exists and password is correct using password_verify()
        if ($user && password_verify($password_input, $user['password'])) {
            // Password is correct! Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];

            // Redirect to homepage or dashboard
            echo "<script>
                    alert('Login successful! Welcome, " . $user['username'] . "');
                    window.location.href = 'index.html';
                  </script>";
            exit();
        } else {
            // Invalid credentials
            echo "<script>
                    alert('Invalid username/email or password.');
                    window.location.href = 'login.html';
                  </script>";
        }

    } catch (PDOException $e) {
        die("Database Error: " . $e->getMessage());
    }

} else {
    header("Location: login.html");
    exit();
}
?>