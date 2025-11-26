<?php
require_once 'config.php';
require_admin();

$error = '';
$success = '';

if (!isset($_GET['id'])) {
    header('Location: manage_jobs.php');
    exit();
}

$job_id = (int)$_GET['id'];

// Get job details
$sql = "SELECT * FROM job_postings WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $job_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header('Location: manage_jobs.php');
    exit();
}

$job = $result->fetch_assoc();

// Handle update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $company_id = (int)$_POST['company_id'];
    $title = clean_input($_POST['title']);
    $description = clean_input($_POST['description']);
    $requirements = clean_input($_POST['requirements']);
    $salary_range = clean_input($_POST['salary_range']);
    $location = clean_input($_POST['location']);
    $job_type = clean_input($_POST['job_type']);
    $application_deadline = clean_input($_POST['application_deadline']);
    $status = clean_input($_POST['status']);
    
    $sql = "UPDATE job_postings SET company_id = ?, title = ?, description = ?, requirements = ?, 
            salary_range = ?, location = ?, job_type = ?, application_deadline = ?, status = ? 
            WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issssssssi", $company_id, $title, $description, $requirements, $salary_range, 
                      $location, $job_type, $application_deadline, $status, $job_id);
    
    if ($stmt->execute()) {
        $success = 'Job posting updated successfully!';
        // Refresh job data
        $stmt = $conn->prepare("SELECT * FROM job_postings WHERE id = ?");
        $stmt->bind_param("i", $job_id);
        $stmt->execute();
        $job = $stmt->get_result()->fetch_assoc();
    } else {
        $error = 'Failed to update job posting.';
    }
}

// Get companies for dropdown
$companies = $conn->query("SELECT id, name FROM companies ORDER BY name");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Job - Placement System</title>
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
                <li><a href="manage_students.php">Students</a></li>
                <li><a href="manage_companies.php">Companies</a></li>
                <li><a href="manage_jobs.php" class="active">Jobs</a></li>
                <li><a href="manage_applications.php">Applications</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="dashboard">
        <div class="container">
            <div class="dashboard-header">
                <h1>Edit Job Posting</h1>
            </div>

            <div class="card">
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="company_id">Company *</label>
                        <select id="company_id" name="company_id" required>
                            <option value="">Select Company</option>
                            <?php while ($company = $companies->fetch_assoc()): ?>
                                <option value="<?php echo $company['id']; ?>" 
                                    <?php echo $job['company_id'] == $company['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($company['name']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="title">Job Title *</label>
                        <input type="text" id="title" name="title" 
                               value="<?php echo htmlspecialchars($job['title']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Job Description *</label>
                        <textarea id="description" name="description" rows="5" required><?php echo htmlspecialchars($job['description']); ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="requirements">Requirements *</label>
                        <textarea id="requirements" name="requirements" rows="4" required><?php echo htmlspecialchars($job['requirements']); ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="salary_range">Salary Range</label>
                        <input type="text" id="salary_range" name="salary_range" 
                               value="<?php echo htmlspecialchars($job['salary_range']); ?>" 
                               placeholder="e.g., $50,000 - $70,000">
                    </div>
                    
                    <div class="form-group">
                        <label for="location">Location *</label>
                        <input type="text" id="location" name="location" 
                               value="<?php echo htmlspecialchars($job['location']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="job_type">Job Type *</label>
                        <select id="job_type" name="job_type" required>
                            <option value="Full-time" <?php echo $job['job_type'] == 'Full-time' ? 'selected' : ''; ?>>Full-time</option>
                            <option value="Internship" <?php echo $job['job_type'] == 'Internship' ? 'selected' : ''; ?>>Internship</option>
                            <option value="Part-time" <?php echo $job['job_type'] == 'Part-time' ? 'selected' : ''; ?>>Part-time</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="application_deadline">Application Deadline *</label>
                        <input type="date" id="application_deadline" name="application_deadline" 
                               value="<?php echo htmlspecialchars($job['application_deadline']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select id="status" name="status">
                            <option value="Active" <?php echo $job['status'] == 'Active' ? 'selected' : ''; ?>>Active</option>
                            <option value="Closed" <?php echo $job['status'] == 'Closed' ? 'selected' : ''; ?>>Closed</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Update Job Posting</button>
                    <a href="manage_jobs.php" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</body>
</html>