<?php
require_once 'config.php';
require_admin();

$error = '';
$success = '';

// Handle add company
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = clean_input($_POST['name']);
    $description = clean_input($_POST['description']);
    $website = clean_input($_POST['website']);
    $contact_person = clean_input($_POST['contact_person']);
    $contact_email = clean_input($_POST['contact_email']);
    $contact_phone = clean_input($_POST['contact_phone']);
    
    $sql = "INSERT INTO companies (name, description, website, contact_person, contact_email, contact_phone) 
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $name, $description, $website, $contact_person, $contact_email, $contact_phone);
    
    if ($stmt->execute()) {
        $success = 'Company added successfully!';
    } else {
        $error = 'Failed to add company.';
    }
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM companies WHERE id = $id");
    header('Location: manage_companies.php');
    exit();
}

// Get all companies
$companies = $conn->query("SELECT * FROM companies ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Companies - Placement System</title>
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
                <li><a href="manage_companies.php" class="active">Companies</a></li>
                <li><a href="manage_jobs.php">Jobs</a></li>
                <li><a href="manage_applications.php">Applications</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="dashboard">
        <div class="container">
            <div class="dashboard-header">
                <h1>Manage Companies</h1>
            </div>

            <!-- Add Company Form -->
            <div class="card">
                <h3>Add New Company</h3>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="name">Company Name *</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" rows="3"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="website">Website</label>
                        <input type="url" id="website" name="website" placeholder="https://">
                    </div>
                    
                    <div class="form-group">
                        <label for="contact_person">Contact Person</label>
                        <input type="text" id="contact_person" name="contact_person">
                    </div>
                    
                    <div class="form-group">
                        <label for="contact_email">Contact Email</label>
                        <input type="email" id="contact_email" name="contact_email">
                    </div>
                    
                    <div class="form-group">
                        <label for="contact_phone">Contact Phone</label>
                        <input type="tel" id="contact_phone" name="contact_phone">
                    </div>
                    
                    <button type="submit" class="btn btn-apply">Add Company</button>
                </form>
            </div>

            <!-- Companies List -->
            <div class="card">
                <h3>All Companies</h3>
                <?php if ($companies->num_rows > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Company Name</th>
                                <th>Contact Person</th>
                                <th>Contact Email</th>
                                <th>Contact Phone</th>
                                <th>Website</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($company = $companies->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($company['name']); ?></td>
                                    <td><?php echo htmlspecialchars($company['contact_person']); ?></td>
                                    <td><?php echo htmlspecialchars($company['contact_email']); ?></td>
                                    <td><?php echo htmlspecialchars($company['contact_phone']); ?></td>
                                    <td>
                                        <?php if ($company['website']): ?>
                                            <a href="<?php echo htmlspecialchars($company['website']); ?>" target="_blank">Visit</a>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="edit_company.php?id=<?php echo $company['id']; ?>" class="btn btn-info" style="padding: 5px 10px; font-size: 0.85rem;">Edit</a>
                                        <a href="manage_companies.php?delete=<?php echo $company['id']; ?>" class="btn btn-danger" style="padding: 5px 10px; font-size: 0.85rem;" onclick="return confirm('Are you sure? This will also delete all jobs from this company.')">Delete</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No companies added yet.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>