<?php
//  database connection and start session
require_once '../database/db.php';
session_start();

// only logged in users can add students
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

$message = "";

// AJAX request or form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $profile_picture_path = null;

    $errors = [];

    // validation
    if (empty($name) || empty($email) || empty($phone) || empty($address)) {
        $errors[] = "Please fill in all required text fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    // handle file apload and check foe text errors
    if (empty($errors)) {
        if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['profile_picture']['tmp_name'];
            $fileName = $_FILES['profile_picture']['name'];
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            // extensions allowed
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

    // check if this request came from JavaScript AJAX
    $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

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
        // insert into database 
        try {
            $stmt = $pdo->prepare("INSERT INTO students (name, email, phone, address, profile_picture) VALUES (?, ?, ?, ?, ?)");
            if ($stmt->execute([$name, $email, $phone, $address, $profile_picture_path])) {
                if ($isAjax) {
                    header('Content-Type: application/json');
                    echo json_encode(['status' => 'success', 'message' => 'Student added successfully!']);
                    exit();
                } else {
                    $message = "<div class='alert alert-success'>Student added successfully! <a href='view_students.php'>View Students</a></div>";
                }
            } else {
                $errorMsg = "Failed to save student record to database.";
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
    <title>Add Student - Student Registration System</title>
    <!-- bootstrap-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- css style sheet -->
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body class="bg-light">

    <!-- responsive navigation bar -->
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

    <!-- add students -->
    <div class="container mt-5 mb-5">
        <div class="row justify-content-center">
            <div class="col-md-7">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        <h3 class="text-center mb-4 fw-bold text-primary">Add New Student</h3>

                        <!-- AJAX response message  -->
                        <div id="ajax-message"></div>

                        <!-- php feedback message -->
                        <?php echo $message; ?>

                        <!-- form -->
                        <form id="addStudentForm" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>

                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone Number <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="phone" name="phone" required>
                            </div>

                            <div class="mb-3">
                                <label for="address" class="form-label">Address <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="address" name="address" rows="3" required></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="profile_picture" class="form-label">Profile Picture (Optional)</label>
                                <input type="file" class="form-control" id="profile_picture" name="profile_picture" accept="image/*">
                            </div>

                            <div class="d-grid">
                                <button type="submit" id="submitBtn" class="btn btn-success">Save Student Record</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- js -->
     <script src="../assets/js/script.js"></script>

</body>
</html>