    <?php
// Include database connection and start session
require_once 'db.php';
session_start();

// Protect page so only logged-in users can delete records
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

// Check if student ID is provided in the URL
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $student_id = $_GET['id'];

    try {
        // 1. Fetch the student record first to check if they have an uploaded profile picture
        $stmt = $pdo->prepare("SELECT profile_picture FROM students WHERE id = ?");
        $stmt->execute([$student_id]);
        $student = $stmt->fetch();

        if ($student) {
            // If a profile picture file exists on the server, delete it to save space
            if (!empty($student['profile_picture']) && file_exists($student['profile_picture'])) {
                unlink($student['profile_picture']);
            }

            // 2. Delete the student record from the database using a prepared statement
            $deleteStmt = $pdo->prepare("DELETE FROM students WHERE id = ?");
            if ($deleteStmt->execute([$student_id])) {
                echo "<script>
                        alert('Student record deleted successfully.');
                        window.location.href = 'view_students.php';
                      </script>";
                exit();
            } else {
                echo "<script>
                        alert('Failed to delete student record.');
                        window.location.href = 'view_students.php';
                      </script>";
                exit();
            }
        } else {
            echo "<script>
                    alert('Student not found.');
                    window.location.href = 'view_students.php';
                  </script>";
            exit();
        }

    } catch (PDOException $e) {
        die("Database Error: " . $e->getMessage());
    }

} else {
    // If no ID is provided, redirect back to view students
    header("Location: view_students.php");
    exit();
}
?>