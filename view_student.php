<?php
require_once 'config.php';
require_admin();

if (!isset($_GET['id'])) {
    header('Location: manage_students.php');
    exit();
}

$student_id = (int)$_GET['id'];

// Get student details
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();

if (!$student) {
    header('Location: manage_students.php');
    exit();
}

// Get student's applications
$apps_sql = "SELECT a.*, jp.title as job_title, c.name as company_name 
             FROM applications a 
             LEFT JOIN job_postings jp ON a.job_id = jp.id 
             LEFT JOIN companies c ON jp.company_id = c.id 
             WHERE a.user_id = ?
             ORDER BY a.applied_at DESC";
$stmt = $conn->prepare($apps_sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$applications = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Student - Placement System</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="nav-brand">
                <h2>PlacementHub - Admin</h2>
            </div>
            <ul class="nav-menu">
                <li><a href="admin_dashboard.php">Dashboard</a></li>
                <li><a href="manage_students.php" class="active">Students</a></li>
                <li><a href="manage_companies.php">Companies</a></li>
                <li><a href="manage_jobs.php">Jobs</a></li>
                <li><a href="manage_applications.php">Applications</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="dashboard">
        <div class="container">
            <div class="card">
                <h2>Student Details</h2>
                
                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; margin-top: 1rem;">
                    <div>
                        <p><strong>Name:</strong> <?php echo htmlspecialchars($student['name']); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($student['email']); ?></p>
                        <p><strong>Phone:</strong> <?php echo htmlspecialchars($student['phone']); ?></p>
                        <p><strong>Roll Number:</strong> <?php echo htmlspecialchars($student['roll_number']); ?></p>
                    </div>
                    <div>
                        <p><strong>Department:</strong> <?php echo htmlspecialchars($student['department']); ?></p>
                        <p><strong>Graduation Year:</strong> <?php echo htmlspecialchars($student['graduation_year']); ?></p>
                        <p><strong>CGPA:</strong> <?php echo htmlspecialchars($student['cgpa']); ?></p>
                        <p><strong>Registered:</strong> <?php echo date('M d, Y', strtotime($student['created_at'])); ?></p>
                    </div>
                </div>
                
                <a href="manage_students.php" class="btn btn-secondary" style="margin-top: 1rem;">Back to Students</a>
            </div>

            <div class="card">
                <h3>Application History</h3>
                <?php if ($applications->num_rows > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Company</th>
                                <th>Job Title</th>
                                <th>Applied On</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($app = $applications->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($app['company_name']); ?></td>
                                    <td><?php echo htmlspecialchars($app['job_title']); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($app['applied_at'])); ?></td>
                                    <td>
                                        <span class="badge badge-<?php echo strtolower($app['status']); ?>">
                                            <?php echo $app['status']; ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No applications yet.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>