<?php
// start session and protect page
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: authentication\login.html");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Student Registration System</title>
    <!-- bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- css style sheet -->
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body class="bg-light">

    <!-- navigation bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">Student System</a>
            
            <!-- mobile toggle button -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <!-- menu -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <div class="navbar-nav ms-auto">
                    <a class="nav-link active" href="index.php">Home</a>
                    <a class="nav-link" href="students_crud\view_students.php">View Students</a>
                    <a class="nav-link text-danger" href="authentication\logout.php">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- hero section -->
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8 text-center">
                <div class="card shadow-sm border-0 p-5">
                    <div class="card-body">
                        <h1 class="display-5 fw-bold text-primary mb-3">Student Registration System</h1>
                        <p class="lead text-muted mb-4">
                            Welcome back, <strong><?php echo htmlspecialchars($_SESSION['username'] ?? 'User'); ?></strong>! Manage student records, perform CRUD operations, and explore secure user authentication effortlessly.
                        </p>
                        
                        <!-- action buttons -->
                        <div class="d-flex justify-content-center gap-3 flex-wrap">
                            <a href="students_crud\add_student.php" class="btn btn-success px-4">Add Student</a>
                            <a href="students_crud\view_students.php" class="btn btn-secondary px-4">View Students</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- js -->
     <script src="assets/js/script.js"></script>
</body>
</html>