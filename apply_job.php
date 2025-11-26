<?php
require_once 'config.php';
require_login();

$error = '';
$success = '';
$user_id = $_SESSION['user_id'];

if (!isset($_GET['id'])) {
    header('Location: student_dashboard.php');
    exit();
}

$job_id = (int)$_GET['id'];

// Get job details
$sql = "SELECT jp.*, c.name as company_name 
        FROM job_postings jp 
        LEFT JOIN companies c ON jp.company_id = c.id 
        WHERE jp.id = ? AND jp.status = 'Active'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $job_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header('Location: student_dashboard.php');
    exit();
}

$job = $result->fetch_assoc();

// Check if already applied
$check_sql = "SELECT id FROM applications WHERE user_id = ? AND job_id = ?";
$stmt = $conn->prepare($check_sql);
$stmt->bind_param("ii", $user_id, $job_id);
$stmt->execute();
$already_applied = $stmt->get_result()->num_rows > 0;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !$already_applied) {
    $cover_letter = clean_input($_POST['cover_letter']);
    
    $insert_sql = "INSERT INTO applications (user_id, job_id, cover_letter) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($insert_sql);
    $stmt->bind_param("iis", $user_id, $job_id, $cover_letter);
    
    if ($stmt->execute()) {
        $success = 'Application submitted successfully!';
        $already_applied = true;
    } else {
        $error = 'Failed to submit application. Please try again.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apply for Job - Placement System</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="nav-brand">
                <h2>PlacementHub</h2>
            </div>
            <ul class="nav-menu">
                <li><a href="student_dashboard.php">Dashboard</a></li>
                <li><a href="student_profile.php">Profile</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="dashboard">
        <div class="container">
            <div class="card">
                <h2><?php echo htmlspecialchars($job['title']); ?></h2>
                <p><strong>Company:</strong> <?php echo htmlspecialchars($job['company_name']); ?></p>
                <p><strong>Location:</strong> <?php echo htmlspecialchars($job['location']); ?></p>
                <p><strong>Salary Range:</strong> <?php echo htmlspecialchars($job['salary_range']); ?></p>
                <p><strong>Job Type:</strong> <?php echo htmlspecialchars($job['job_type']); ?></p>
                <p><strong>Application Deadline:</strong> <?php echo date('M d, Y', strtotime($job['application_deadline'])); ?></p>
                
                <h3>Job Description</h3>
                <p><?php echo nl2br(htmlspecialchars($job['description'])); ?></p>
                
                <h3>Requirements</h3>
                <p><?php echo nl2br(htmlspecialchars($job['requirements'])); ?></p>
            </div>

            <div class="card">
                <h3>Apply for this Position</h3>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <?php echo $success; ?>
                        <br><a href="student_dashboard.php">Back to Dashboard</a>
                    </div>
                <?php endif; ?>
                
                <?php if ($already_applied && !$success): ?>
                    <div class="alert alert-info">
                        You have already applied for this position.
                        <br><a href="student_dashboard.php">Back to Dashboard</a>
                    </div>
                <?php else: ?>
                    <?php if (!$success): ?>
                        <form method="POST" action="">
                            <div class="form-group">
                                <label for="cover_letter">Cover Letter / Why should we hire you?</label>
                                <textarea id="cover_letter" name="cover_letter" rows="8" required></textarea>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">Submit Application</button>
                            <a href="student_dashboard.php" class="btn btn-secondary">Cancel</a>
                        </form>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>