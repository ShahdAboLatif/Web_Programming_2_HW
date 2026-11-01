<?php
// database connection
require_once '../database/db.php';

// start session
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username_input = trim($_POST['username'] ?? '');
    $password_input = $_POST['password'] ?? '';

    // convert input to lowercase if user signed in with email
    $email_input = strtolower($username_input);

    $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

    if (empty($username_input) || empty($password_input)) {
        $errorMsg = "Please fill in all fields.";
        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => $errorMsg]);
            exit();
        } else {
            die($errorMsg . " <a href='login.html'>Go back</a>");
        }
    }

    try {
        // find user by username or email
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username_input, $email_input]);
        $user = $stmt->fetch();

        // verify credentials
        if ($user && password_verify($password_input, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];

            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Login successful! Welcome, ' . htmlspecialchars($user['username']) . '. Redirecting...'
                ]);
                exit();
            } else {
                echo "<script>
                        alert('Login successful! Welcome, " . htmlspecialchars($user['username']) . "');
                        window.location.href = '../index.php';
                      </script>";
                exit();
            }
        } else {
            $errorMsg = "Invalid username/email or password.";
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'message' => $errorMsg]);
                exit();
            } else {
                echo "<script>
                        alert('Invalid username/email or password.');
                        window.location.href = 'login.html';
                      </script>";
                exit();
            }
        }

    } catch (PDOException $e) {
        $errorMsg = "Database Error: " . $e->getMessage();
        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => $errorMsg]);
            exit();
        } else {
            die($errorMsg);
        }
    }

} else {
    header("Location: login.html");
    exit();
}
?>