<?php
require_once 'config.php';
require_admin();

$error = '';
$success = '';

// Handle add job
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
    
    $sql = "INSERT INTO job_postings (company_id, title, description, requirements, salary_range, location, job_type, application_deadline, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issssssss", $company_id, $title, $description, $requirements, $salary_range, $location, $job_type, $application_deadline, $status);
    
    if ($stmt->execute()) {
        $success = 'Job posting added successfully!';
    } else {
        $error = 'Failed to add job posting.';
    }
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM job_postings WHERE id = $id");
    header('Location: manage_jobs.php');
    exit();
}

// Get all jobs
$jobs_sql = "SELECT jp.*, c.name as company_name 
             FROM job_postings jp 
             LEFT JOIN companies c ON jp.company_id = c.id 
             ORDER BY jp.created_at DESC";
$jobs = $conn->query($jobs_sql);

// Get companies for dropdown
$companies = $conn->query("SELECT id, name FROM companies ORDER BY name");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Jobs - Placement System</title>
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
                <h1>Manage Job Postings</h1>
            </div>

            <!-- Add Job Form -->
            <div class="card">
                <h3>Add New Job Posting</h3>
                
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
                            <?php 
                            $companies_copy = $conn->query("SELECT id, name FROM companies ORDER BY name");
                            while ($company = $companies_copy->fetch_assoc()): 
                            ?>
                                <option value="<?php echo $company['id']; ?>"><?php echo htmlspecialchars($company['name']); ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="title">Job Title *</label>
                        <input type="text" id="title" name="title" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Job Description *</label>
                        <textarea id="description" name="description" rows="5" required></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="requirements">Requirements *</label>
                        <textarea id="requirements" name="requirements" rows="4" required></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="salary_range">Salary Range</label>
                        <input type="text" id="salary_range" name="salary_range" placeholder="e.g., $50,000 - $70,000">
                    </div>
                    
                    <div class="form-group">
                        <label for="location">Location *</label>
                        <input type="text" id="location" name="location" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="job_type">Job Type *</label>
                        <select id="job_type" name="job_type" required>
                            <option value="Full-time">Full-time</option>
                            <option value="Internship">Internship</option>
                            <option value="Part-time">Part-time</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="application_deadline">Application Deadline *</label>
                        <input type="date" id="application_deadline" name="application_deadline" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select id="status" name="status">
                            <option value="Active">Active</option>
                            <option value="Closed">Closed</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn btn-apply">Add Job Posting</button>
                </form>
            </div>

            <!-- Jobs List -->
            <div class="card">
                <h3>All Job Postings</h3>
                <?php if ($jobs->num_rows > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Company</th>
                                <th>Job Title</th>
                                <th>Location</th>
                                <th>Type</th>
                                <th>Deadline</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($job = $jobs->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($job['company_name']); ?></td>
                                    <td><?php echo htmlspecialchars($job['title']); ?></td>
                                    <td><?php echo htmlspecialchars($job['location']); ?></td>
                                    <td><?php echo htmlspecialchars($job['job_type']); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($job['application_deadline'])); ?></td>
                                    <td>
                                        <span class="badge badge-<?php echo strtolower($job['status']); ?>">
                                            <?php echo $job['status']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="edit_job.php?id=<?php echo $job['id']; ?>" class="btn btn-info" style="padding: 5px 10px; font-size: 0.85rem;">Edit</a>
                                        <a href="manage_jobs.php?delete=<?php echo $job['id']; ?>" class="btn btn-danger" style="padding: 5px 10px; font-size: 0.85rem;" onclick="return confirm('Are you sure? This will also delete all applications.')">Delete</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No job postings yet.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>