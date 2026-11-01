<?php
// Include database connection and start session
require_once '../database/db.php';
session_start();

// Only logged in users can update records
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

$message = "";
$student = null;

// Check student ID from GET request
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $student_id = $_GET['id'];

    try {
        $stmt = $pdo->prepare("SELECT * FROM students WHERE id = ?");
        $stmt->execute([$student_id]);
        $student = $stmt->fetch();

        if (!$student) {
            die("Student not found. <a href='view_students.php'>Back to View Students</a>");
        }
    } catch (PDOException $e) {
        die("Database Error: " . $e->getMessage());
    }
} else {
    header("Location: view_students.php");
    exit();
}

// Form submission for update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $profile_picture_path = $student['profile_picture']; 

    $errors = [];

    // Validation checks
    if (empty($name) || empty($email) || empty($phone) || empty($address)) {
        $errors[] = "Please fill in all required text fields.";
    }

    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    // Phone validation: digits only, 10 to 15 digits
    if (!empty($phone) && !preg_match('/^[0-9]{10,15}$/', $phone)) {
        $errors[] = "Phone number must contain only numbers and be between 10 to 15 digits.";
    }

    // File upload handling
    if (empty($errors)) {
        if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['profile_picture']['tmp_name'];
            $fileName = $_FILES['profile_picture']['name'];
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

            if (in_array($fileExtension, $allowedExtensions)) {
                $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
                $uploadFileDir = '../uploads/';
                $dest_path = $uploadFileDir . $newFileName;

                if (move_uploaded_file($fileTmpPath, $dest_path)) {
                    $profile_picture_path = $dest_path; 
                } else {
                    $errors[] = "Error moving the uploaded file.";
                }
            } else {
                $errors[] = "Invalid file type. Only JPG, JPEG, PNG, and GIF are allowed.";
            }
        }
    }

    // Check if request came via AJAX
    $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

    if (!empty($errors)) {
        $errorString = implode("<br>", $errors);
        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => $errorString]);
            exit();
        } else {
            $message = "<div class='alert alert-danger'>$errorString</div>";
        }
    } else {
        try {
            $updateStmt = $pdo->prepare("UPDATE students SET name = ?, email = ?, phone = ?, address = ?, profile_picture = ? WHERE id = ?");
            if ($updateStmt->execute([$name, $email, $phone, $address, $profile_picture_path, $student_id])) {
                if ($isAjax) {
                    header('Content-Type: application/json');
                    echo json_encode(['status' => 'success', 'message' => 'Student record updated successfully! Redirecting...']);
                    exit();
                } else {
                    echo "<script>
                            alert('Student record updated successfully!');
                            window.location.href = 'view_students.php';
                          </script>";
                    exit();
                }
            } else {
                $errorMsg = "Failed to update student record in the database.";
                if ($isAjax) {
                    header('Content-Type: application/json');
                    echo json_encode(['status' => 'error', 'message' => $errorMsg]);
                    exit();
                } else {
                    $message = "<div class='alert alert-danger'>$errorMsg</div>";
                }
            }
        } catch (PDOException $e) {
            $errorMsg = "Database Error: " . $e->getMessage();
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'message' => $errorMsg]);
                exit();
            } else {
                $message = "<div class='alert alert-danger'>$errorMsg</div>";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Student - Student Registration System</title>
    <!-- bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <!-- css style sheet -->
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body class="bg-light">

    <!-- navigation bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="../index.php">Student System</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <div class="navbar-nav ms-auto">
                    <a class="nav-link" href="../index.php">Home</a>
                    <a class="nav-link" href="view_students.php">View Students</a>
                    <a class="nav-link text-danger" href="../authentication/logout.php">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- update students form -->
    <div class="container mt-5 mb-5">
        <div class="row justify-content-center">
            <div class="col-md-7">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        <h3 class="text-center mb-4 fw-bold text-primary">Update Student Record</h3>

                        <!-- AJAX response message container -->
                        <div id="ajax-message"></div>

                        <!-- php feedback message -->
                        <?php echo $message; ?>

                        <!-- pre-filled values form -->
                        <form id="updateStudentForm" action="update_student.php?id=<?php echo $student['id']; ?>" method="POST" enctype="multipart/form-data" novalidate>
                            
                            <div class="mb-3">
                                <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($student['name']); ?>" required>
                                <div class="invalid-feedback">Full Name is required.</div>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($student['email']); ?>" required>
                                <div class="invalid-feedback" id="email-error">Please enter a valid email address.</div>
                            </div>

                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone Number <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($student['phone']); ?>" required>
                                <div class="invalid-feedback" id="phone-error">Phone number must be digits only (10 to 15 digits).</div>
                            </div>

                            <div class="mb-3">
                                <label for="address" class="form-label">Address <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="address" name="address" rows="3" required><?php echo htmlspecialchars($student['address']); ?></textarea>
                                <div class="invalid-feedback">Address is required.</div>
                            </div>

                            <!-- profile picture section -->
                            <div class="mb-3">
                                <label for="profile_picture" class="form-label">Profile Picture (Optional)</label>
                                <?php if (!empty($student['profile_picture'])): ?>
                                    <div class="mb-2">
                                        <img src="<?php echo htmlspecialchars($student['profile_picture']); ?>" alt="Current Profile" class="rounded" width="60" height="60" style="object-fit: cover;">
                                        <small class="text-muted d-block">Current picture shown above. Upload a new one to replace it.</small>
                                    </div>
                                <?php endif; ?>
                                <input type="file" class="form-control" id="profile_picture" name="profile_picture" accept="image/*">
                                <div class="invalid-feedback" id="file-error">Allowed image extensions are: JPG, JPEG, PNG, GIF.</div>
                            </div>

                            <!-- update & cancel buttons -->
                            <div class="d-flex justify-content-between mt-4">
                                <a href="view_students.php" class="btn btn-secondary px-4">Cancel</a>
                                <button type="submit" id="submitBtn" class="btn btn-primary px-4">Update Record</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- js -->
    <script src="../assets/js/script.js"></script>
</body>
</html>