<?php
require_once 'config.php';
require_admin();

$error = '';
$success = '';

if (!isset($_GET['id'])) {
    header('Location: manage_companies.php');
    exit();
}

$company_id = (int)$_GET['id'];

// Get company details
$sql = "SELECT * FROM companies WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $company_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header('Location: manage_companies.php');
    exit();
}

$company = $result->fetch_assoc();

// Handle update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = clean_input($_POST['name']);
    $description = clean_input($_POST['description']);
    $website = clean_input($_POST['website']);
    $contact_person = clean_input($_POST['contact_person']);
    $contact_email = clean_input($_POST['contact_email']);
    $contact_phone = clean_input($_POST['contact_phone']);
    
    $sql = "UPDATE companies SET name = ?, description = ?, website = ?, contact_person = ?, 
            contact_email = ?, contact_phone = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssi", $name, $description, $website, $contact_person, 
                      $contact_email, $contact_phone, $company_id);
    
    if ($stmt->execute()) {
        $success = 'Company updated successfully!';
        // Refresh company data
        $stmt = $conn->prepare("SELECT * FROM companies WHERE id = ?");
        $stmt->bind_param("i", $company_id);
        $stmt->execute();
        $company = $stmt->get_result()->fetch_assoc();
    } else {
        $error = 'Failed to update company.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Company - Placement System</title>
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
                <h1>Edit Company</h1>
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
                        <label for="name">Company Name *</label>
                        <input type="text" id="name" name="name" 
                               value="<?php echo htmlspecialchars($company['name']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" rows="3"><?php echo htmlspecialchars($company['description']); ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="website">Website</label>
                        <input type="url" id="website" name="website" 
                               value="<?php echo htmlspecialchars($company['website']); ?>" 
                               placeholder="https://">
                    </div>
                    
                    <div class="form-group">
                        <label for="contact_person">Contact Person</label>
                        <input type="text" id="contact_person" name="contact_person" 
                               value="<?php echo htmlspecialchars($company['contact_person']); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="contact_email">Contact Email</label>
                        <input type="email" id="contact_email" name="contact_email" 
                               value="<?php echo htmlspecialchars($company['contact_email']); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="contact_phone">Contact Phone</label>
                        <input type="tel" id="contact_phone" name="contact_phone" 
                               value="<?php echo htmlspecialchars($company['contact_phone']); ?>">
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Update Company</button>
                    <a href="manage_companies.php" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</body>
</html>