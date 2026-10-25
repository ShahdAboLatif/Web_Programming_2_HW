<?php
// Include database connection and start session
require_once 'db.php';
session_start();

// Protect page so only logged-in users can view students
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

// Pagination and Search configuration
$limit = 10; // Number of students per page
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$search = isset($_GET['search']) ? trim($_GET['search']) : '';

try {
    if (!empty($search)) {
        // Count total matching records for pagination
        $countStmt = $pdo->prepare("SELECT COUNT(*) FROM students WHERE name LIKE ? OR email LIKE ? OR phone LIKE ?");
        $searchTerm = "%$search%";
        $countStmt->execute([$searchTerm, $searchTerm, $searchTerm]);
        $totalRecords = $countStmt->fetchColumn();

        // Fetch matching records with limit and offset
        $stmt = $pdo->prepare("SELECT * FROM students WHERE name LIKE ? OR email LIKE ? OR phone LIKE ? ORDER BY created_at DESC LIMIT " . (int)$limit . " OFFSET " . (int)$offset);
        $stmt->execute([$searchTerm, $searchTerm, $searchTerm]);
    } else {
        // Count total records
        $countStmt = $pdo->query("SELECT COUNT(*) FROM students");
        $totalRecords = $countStmt->fetchColumn();

        // Fetch all records with limit and offset
        $stmt = $pdo->prepare("SELECT * FROM students ORDER BY created_at DESC LIMIT " . (int)$limit . " OFFSET " . (int)$offset);
        $stmt->execute();
    }

    $students = $stmt->fetchAll();
    $totalPages = max(1, ceil($totalRecords / $limit));

} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Students - Student Registration System</title>
    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body class="bg-light">

    <!-- Responsive Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.html">Student System</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <div class="navbar-nav ms-auto">
                    <a class="nav-link" href="index.html">Home</a>
                    <a class="nav-link active" href="view_students.php">View Students</a>
                    <a class="nav-link text-danger" href="logout.php">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content Container -->
    <div class="container mt-4 mb-5">
        <!-- Header Section -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
            <h2 class="fw-bold text-primary m-0">Student Records</h2>
            <a href="add_student.php" class="btn btn-success">
                <i class="bi bi-plus-circle"></i> Add New Student
            </a>
        </div>

        <!-- Search Bar Form -->
        <form method="GET" action="view_students.php" class="mb-4">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Search by name, email, or phone number..." value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-search"></i> Search
                </button>
                <?php if (!empty($search)): ?>
                    <a href="view_students.php" class="btn btn-outline-secondary">Reset</a>
                <?php endif; ?>
            </div>
        </form>

        <!-- Students Table Card -->
        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>Picture</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Address</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($students) > 0): ?>
                                <?php foreach ($students as $student): ?>
                                    <tr>
                                        <td>
                                            <?php if (!empty($student['profile_picture'])): ?>
                                                <img src="<?php echo htmlspecialchars($student['profile_picture']); ?>" alt="Profile" class="rounded-circle" width="40" height="40" style="object-fit: cover;">
                                            <?php else: ?>
                                                <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                    <i class="bi bi-person"></i>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="fw-semibold"><?php echo htmlspecialchars($student['name']); ?></td>
                                        <td><?php echo htmlspecialchars($student['email']); ?></td>
                                        <td><?php echo htmlspecialchars($student['phone']); ?></td>
                                        <td><?php echo htmlspecialchars($student['address']); ?></td>
                                        <td class="text-center text-nowrap">
                                            <!-- Update Icon Button -->
                                            <a href="update_student.php?id=<?php echo $student['id']; ?>" class="btn btn-sm btn-outline-primary me-1" title="Update">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                            <!-- Delete Icon Button with JavaScript Confirmation -->
                                            <a href="delete_student.php?id=<?php echo $student['id']; ?>" class="btn btn-sm btn-outline-danger" title="Delete" onclick="return confirm('Are you sure you want to delete this student record?');">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">No student records found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Pagination Controls -->
        <?php if ($totalPages > 1): ?>
            <nav aria-label="Page navigation" class="mt-4">
                <ul class="pagination justify-content-center">
                    <!-- Previous Button -->
                    <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                        <a class="page-link" href="view_students.php?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>">Previous</a>
                    </li>

                    <!-- Page Numbers -->
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
                            <a class="page-link" href="view_students.php?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>

                    <!-- Next Button -->
                    <li class="page-item <?php echo ($page >= $totalPages) ? 'disabled' : ''; ?>">
                        <a class="page-link" href="view_students.php?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>">Next</a>
                    </li>
                </ul>
            </nav>
        <?php endif; ?>

    </div>

    <!-- Bootstrap 5 JS Bundle CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>