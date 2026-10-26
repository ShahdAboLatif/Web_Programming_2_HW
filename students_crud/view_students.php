<?php
// database connection
require_once '..\database\db.php';
session_start();

// protect page
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

// pagination
$limit = 10; // Number of students per page
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$search = isset($_GET['search']) ? trim($_GET['search']) : '';

try {
    if (!empty($search)) {
        // count total matching records for pagination
        $countStmt = $pdo->prepare("SELECT COUNT(*) FROM students WHERE name LIKE ? OR email LIKE ? OR phone LIKE ?");
        $searchTerm = "%$search%";
        $countStmt->execute([$searchTerm, $searchTerm, $searchTerm]);
        $totalRecords = $countStmt->fetchColumn();

        // fetch matching records with limit and offset
        $stmt = $pdo->prepare("SELECT * FROM students WHERE name LIKE ? OR email LIKE ? OR phone LIKE ? ORDER BY created_at DESC LIMIT " . (int)$limit . " OFFSET " . (int)$offset);
        $stmt->execute([$searchTerm, $searchTerm, $searchTerm]);
    } else {
        // count total records
        $countStmt = $pdo->query("SELECT COUNT(*) FROM students");
        $totalRecords = $countStmt->fetchColumn();

        // fetch all records with limit and offset
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
    <!-- bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
   
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body class="bg-light">

    <!-- nav bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="../index.php">Student System</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <div class="navbar-nav ms-auto">
                    <a class="nav-link" href="../index.php">Home</a>
                    <a class="nav-link active" href="view_students.php">View Students</a>
                    <a class="nav-link text-danger" href="..\authentication\logout.php">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- view students -->
    <div class="container mt-4 mb-5">
        <!-- header section -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
            <h2 class="fw-bold text-primary m-0">Student Records</h2>
            <a href="add_student.php" class="btn btn-success">
                <i class="bi bi-plus-circle"></i> Add New Student
            </a>
        </div>

        <!-- search bar -->
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

        <!-- students table -->
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
                                            <!-- update icon button -->
                                            <a href="update_student.php?id=<?php echo $student['id']; ?>" class="btn btn-sm btn-outline-primary me-1" title="Update">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                            <!-- delete icon button -->
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

        <!-- pagination controls -->
        <?php if ($totalPages > 1): ?>
            <nav aria-label="Page navigation" class="mt-4">
                <ul class="pagination justify-content-center">
                    <!-- previous button -->
                    <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                        <a class="page-link" href="view_students.php?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>">Previous</a>
                    </li>

                    <!-- page numbers -->
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
                            <a class="page-link" href="view_students.php?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>

                    <!-- next button -->
                    <li class="page-item <?php echo ($page >= $totalPages) ? 'disabled' : ''; ?>">
                        <a class="page-link" href="view_students.php?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>">Next</a>
                    </li>
                </ul>
            </nav>
        <?php endif; ?>

    </div>

    <!-- bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>