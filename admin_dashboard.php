<?php
require_once 'config.php';
require_admin();

// Get statistics
$total_students = $conn->query("SELECT COUNT(*) as count FROM users")->fetch_assoc()['count'];
$total_companies = $conn->query("SELECT COUNT(*) as count FROM companies")->fetch_assoc()['count'];
$total_jobs = $conn->query("SELECT COUNT(*) as count FROM job_postings")->fetch_assoc()['count'];
$total_applications = $conn->query("SELECT COUNT(*) as count FROM applications")->fetch_assoc()['count'];

// Get recent applications
$recent_apps_sql = "SELECT a.*, u.name as student_name, jp.title as job_title, c.name as company_name 
                    FROM applications a 
                    LEFT JOIN users u ON a.user_id = u.id 
                    LEFT JOIN job_postings jp ON a.job_id = jp.id 
                    LEFT JOIN companies c ON jp.company_id = c.id 
                    ORDER BY a.applied_at DESC LIMIT 10";
$recent_apps = $conn->query($recent_apps_sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Placement System</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="nav-brand">
                <h2>PlacementHub - Admin</h2>
            </div>
            <ul class="nav-menu">
                <li><a href="admin_dashboard.php" class="active">Dashboard</a></li>
                <li><a href="manage_students.php">Students</a></li>
                <li><a href="manage_companies.php">Companies</a></li>
                <li><a href="manage_jobs.php">Jobs</a></li>
                <li><a href="manage_applications.php">Applications</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="dashboard">
        <div class="container">
            <div class="dashboard-header">
                <h1>Admin Dashboard</h1>
                <p>Welcome back, <?php echo htmlspecialchars($_SESSION['admin_username']); ?>!</p>
                <a href="change_password.php" class="btn btn-warning" style="margin-top: 10px;">🔐 Change Password</a>
            </div>

            <!-- Statistics -->
            <div class="feature-grid">
                <div class="feature-card">
                    <div class="feature-icon">👥</div>
                    <h3><?php echo $total_students; ?></h3>
                    <p>Total Students</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🏢</div>
                    <h3><?php echo $total_companies; ?></h3>
                    <p>Total Companies</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">💼</div>
                    <h3><?php echo $total_jobs; ?></h3>
                    <p>Job Postings</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">📝</div>
                    <h3><?php echo $total_applications; ?></h3>
                    <p>Applications</p>
                </div>
            </div>

            <!-- Recent Applications -->
            <div class="card">
                <h3>Recent Applications</h3>
                <?php if ($recent_apps->num_rows > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>Company</th>
                                <th>Job Title</th>
                                <th>Status</th>
                                <th>Applied On</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($app = $recent_apps->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($app['student_name']); ?></td>
                                    <td><?php echo htmlspecialchars($app['company_name']); ?></td>
                                    <td><?php echo htmlspecialchars($app['job_title']); ?></td>
                                    <td>
                                        <span class="badge badge-<?php echo strtolower($app['status']); ?>">
                                            <?php echo $app['status']; ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('M d, Y', strtotime($app['applied_at'])); ?></td>
                                    <td>
                                        <a href="view_application.php?id=<?php echo $app['id']; ?>" class="btn btn-info" style="padding: 5px 10px; font-size: 0.85rem;">View</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No applications yet.</p>
                <?php endif; ?>
            </div>

            <!-- Quick Actions -->
            <div class="card">
                <h3>Quick Actions</h3>
                <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                    <a href="manage_companies.php" class="btn btn-apply">Add New Company</a>
                    <a href="manage_jobs.php" class="btn btn-success">Post New Job</a>
                    <a href="manage_students.php" class="btn btn-info">View All Students</a>
                    <a href="manage_applications.php" class="btn btn-warning">Manage Applications</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>