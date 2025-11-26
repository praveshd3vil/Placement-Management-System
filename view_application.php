<?php
require_once 'config.php';
require_admin();

$success = '';
$error = '';

if (!isset($_GET['id'])) {
    header('Location: manage_applications.php');
    exit();
}

$app_id = (int)$_GET['id'];

// Handle status update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $new_status = clean_input($_POST['status']);
    
    $update_sql = "UPDATE applications SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("si", $new_status, $app_id);
    
    if ($stmt->execute()) {
        $success = 'Application status updated successfully!';
    } else {
        $error = 'Failed to update status.';
    }
}

// Get application details
$sql = "SELECT a.*, 
        u.name as student_name, u.email as student_email, u.phone as student_phone,
        u.roll_number, u.department, u.graduation_year, u.cgpa,
        jp.title as job_title, jp.description as job_description, 
        jp.requirements as job_requirements, jp.salary_range, jp.location, jp.job_type,
        c.name as company_name, c.website as company_website
        FROM applications a 
        LEFT JOIN users u ON a.user_id = u.id 
        LEFT JOIN job_postings jp ON a.job_id = jp.id 
        LEFT JOIN companies c ON jp.company_id = c.id 
        WHERE a.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $app_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header('Location: manage_applications.php');
    exit();
}

$app = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Application - Placement System</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .info-section {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
        }
        .info-section h3 {
            margin-top: 0;
            color: #667eea;
            border-bottom: 2px solid #667eea;
            padding-bottom: 0.5rem;
            margin-bottom: 1rem;
        }
        .info-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
            margin-bottom: 0.75rem;
        }
        .info-item {
            padding: 0.5rem 0;
        }
        .info-item strong {
            color: #333;
            display: block;
            margin-bottom: 0.25rem;
            font-size: 0.9rem;
        }
        .info-item span {
            color: #666;
        }
        .cover-letter-box {
            background: white;
            border: 1px solid #ddd;
            padding: 1.5rem;
            border-radius: 8px;
            white-space: pre-wrap;
            line-height: 1.6;
            color: #333;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="nav-brand">
                <h2>PlacementHub - Admin</h2>
            </div>
            <ul class="nav-menu">
                <li><a href="admin_dashboard.php">Dashboard</a></li>
                <li><a href="manage_students.php">Students</a></li>
                <li><a href="manage_companies.php">Companies</a></li>
                <li><a href="manage_jobs.php">Jobs</a></li>
                <li><a href="manage_applications.php" class="active">Applications</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="dashboard">
        <div class="container">
            <div class="dashboard-header">
                <h1>Application Details</h1>
                <a href="manage_applications.php" class="btn btn-secondary">← Back to Applications</a>
            </div>

            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <!-- Student Information -->
            <div class="card">
                <div class="info-section">
                    <h3>📋 Student Information</h3>
                    <div class="info-row">
                        <div class="info-item">
                            <strong>Name:</strong>
                            <span><?php echo htmlspecialchars($app['student_name']); ?></span>
                        </div>
                        <div class="info-item">
                            <strong>Roll Number:</strong>
                            <span><?php echo htmlspecialchars($app['roll_number']); ?></span>
                        </div>
                        <div class="info-item">
                            <strong>Email:</strong>
                            <span><?php echo htmlspecialchars($app['student_email']); ?></span>
                        </div>
                        <div class="info-item">
                            <strong>Phone:</strong>
                            <span><?php echo htmlspecialchars($app['student_phone']); ?></span>
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="info-item">
                            <strong>Department:</strong>
                            <span><?php echo htmlspecialchars($app['department']); ?></span>
                        </div>
                        <div class="info-item">
                            <strong>Graduation Year:</strong>
                            <span><?php echo htmlspecialchars($app['graduation_year']); ?></span>
                        </div>
                        <div class="info-item">
                            <strong>CGPA:</strong>
                            <span><?php echo htmlspecialchars($app['cgpa']); ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Job Information -->
            <div class="card">
                <div class="info-section">
                    <h3>💼 Job Information</h3>
                    <div class="info-row">
                        <div class="info-item">
                            <strong>Company:</strong>
                            <span><?php echo htmlspecialchars($app['company_name']); ?></span>
                        </div>
                        <div class="info-item">
                            <strong>Job Title:</strong>
                            <span><?php echo htmlspecialchars($app['job_title']); ?></span>
                        </div>
                        <div class="info-item">
                            <strong>Location:</strong>
                            <span><?php echo htmlspecialchars($app['location']); ?></span>
                        </div>
                        <div class="info-item">
                            <strong>Job Type:</strong>
                            <span><?php echo htmlspecialchars($app['job_type']); ?></span>
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="info-item">
                            <strong>Salary Range:</strong>
                            <span><?php echo htmlspecialchars($app['salary_range']); ?></span>
                        </div>
                        <?php if ($app['company_website']): ?>
                        <div class="info-item">
                            <strong>Company Website:</strong>
                            <span><a href="<?php echo htmlspecialchars($app['company_website']); ?>" target="_blank">Visit Website</a></span>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Application Information -->
            <div class="card">
                <div class="info-section">
                    <h3>📄 Application Information</h3>
                    <div class="info-row">
                        <div class="info-item">
                            <strong>Applied On:</strong>
                            <span><?php echo date('F d, Y \a\t h:i A', strtotime($app['applied_at'])); ?></span>
                        </div>
                        <div class="info-item">
                            <strong>Current Status:</strong>
                            <span class="badge badge-<?php echo strtolower($app['status']); ?>">
                                <?php echo $app['status']; ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cover Letter -->
            <div class="card">
                <h3>Cover Letter / Why should we hire you?</h3>
                <div class="cover-letter-box">
                    <?php echo $app['cover_letter'] ? htmlspecialchars($app['cover_letter']) : 'No cover letter provided.'; ?>
                </div>
            </div>

            <!-- Update Status -->
            <div class="card">
                <h3>Update Application Status</h3>
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select id="status" name="status" class="form-control" required>
                            <option value="Pending" <?php echo $app['status'] === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="Shortlisted" <?php echo $app['status'] === 'Shortlisted' ? 'selected' : ''; ?>>Shortlisted</option>
                            <option value="Selected" <?php echo $app['status'] === 'Selected' ? 'selected' : ''; ?>>Selected</option>
                            <option value="Rejected" <?php echo $app['status'] === 'Rejected' ? 'selected' : ''; ?>>Rejected</option>
                        </select>
                    </div>
                    <button type="submit" name="update_status" class="btn btn-primary">Update Status</button>
                    <a href="manage_applications.php" class="btn btn-secondary">Back to Applications</a>
                </form>
            </div>
        </div>
    </div>
</body>
</html>