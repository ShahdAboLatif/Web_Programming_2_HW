    <?php
// database connection
require_once 'db.php';
session_start();

// loged in check
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

// check for student id in the url
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $student_id = $_GET['id'];

    try {
        // check for profile picture in the url
        $stmt = $pdo->prepare("SELECT profile_picture FROM students WHERE id = ?");
        $stmt->execute([$student_id]);
        $student = $stmt->fetch();

        if ($student) {
            // if a profile pic exist on the server delete to save space
            if (!empty($student['profile_picture']) && file_exists($student['profile_picture'])) {
                unlink($student['profile_picture']);
            }

            // delete student record
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
    // if no id for the student is provided with the url then redirect to the view students page
    header("Location: view_students.php");
    exit();
}
?>