<?php
require_once 'config.php';
require_login();

// Get user details
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Get active job postings
$jobs_sql = "SELECT jp.*, c.name as company_name 
             FROM job_postings jp 
             LEFT JOIN companies c ON jp.company_id = c.id 
             WHERE jp.status = 'Active' AND jp.application_deadline >= CURDATE()
             ORDER BY jp.created_at DESC";
$jobs_result = $conn->query($jobs_sql);

// Get user's applications
$apps_sql = "SELECT a.*, jp.title as job_title, c.name as company_name 
             FROM applications a 
             LEFT JOIN job_postings jp ON a.job_id = jp.id 
             LEFT JOIN companies c ON jp.company_id = c.id 
             WHERE a.user_id = ?
             ORDER BY a.applied_at DESC";
$stmt = $conn->prepare($apps_sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$apps_result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - Placement System</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="nav-brand">
                <h2>PlacementHub</h2>
            </div>
            <ul class="nav-menu">
                <li><a href="student_dashboard.php" class="active">Dashboard</a></li>
                <li><a href="student_profile.php">Profile</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="dashboard">
        <div class="container">
            <div class="dashboard-header">
                <h1>Welcome, <?php echo htmlspecialchars($user['name']); ?>!</h1>
                <p>Roll Number: <?php echo htmlspecialchars($user['roll_number']); ?> | Department: <?php echo htmlspecialchars($user['department']); ?></p>
            </div>

            <!-- My Applications -->
            <div class="card">
                <h3>My Applications</h3>
                <?php if ($apps_result->num_rows > 0): ?>
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
                            <?php while ($app = $apps_result->fetch_assoc()): ?>
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
                    <p>You haven't applied to any jobs yet.</p>
                <?php endif; ?>
            </div>

            <!-- Available Jobs -->
            <div class="card">
                <h3>Available Job Opportunities</h3>
                <?php if ($jobs_result->num_rows > 0): ?>
                    <?php while ($job = $jobs_result->fetch_assoc()): ?>
                        <div class="card" style="margin-bottom: 1rem; border-left: 4px solid #667eea;">
                            <h4><?php echo htmlspecialchars($job['title']); ?></h4>
                            <p><strong>Company:</strong> <?php echo htmlspecialchars($job['company_name']); ?></p>
                            <p><strong>Location:</strong> <?php echo htmlspecialchars($job['location']); ?></p>
                            <p><strong>Salary:</strong> <?php echo htmlspecialchars($job['salary_range']); ?></p>
                            <p><strong>Type:</strong> <?php echo htmlspecialchars($job['job_type']); ?></p>
                            <p><strong>Deadline:</strong> <?php echo date('M d, Y', strtotime($job['application_deadline'])); ?></p>
                            <p><?php echo nl2br(htmlspecialchars(substr($job['description'], 0, 200))); ?>...</p>
                            <a href="apply_job.php?id=<?php echo $job['id']; ?>" class="btn btn-apply">Apply Now</a>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>No active job postings available at the moment.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>