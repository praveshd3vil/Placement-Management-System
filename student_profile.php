<?php
require_once 'config.php';
require_login();

$user_id = $_SESSION['user_id'];
$error = '';
$success = '';

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = clean_input($_POST['name']);
    $phone = clean_input($_POST['phone']);
    $department = clean_input($_POST['department']);
    $graduation_year = clean_input($_POST['graduation_year']);
    $cgpa = clean_input($_POST['cgpa']);
    
    $update_sql = "UPDATE users SET name = ?, phone = ?, department = ?, graduation_year = ?, cgpa = ? WHERE id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("sssidi", $name, $phone, $department, $graduation_year, $cgpa, $user_id);
    
    if ($stmt->execute()) {
        $success = 'Profile updated successfully!';
        $_SESSION['user_name'] = $name;
    } else {
        $error = 'Failed to update profile.';
    }
}

// Get current user data
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Placement System</title>
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
                <li><a href="student_profile.php" class="active">Profile</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="dashboard">
        <div class="container">
            <div class="card">
                <h2>My Profile</h2>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="name">Full Name</label>
                        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email (Cannot be changed)</label>
                        <input type="email" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
                    </div>
                    
                    <div class="form-group">
                        <label for="roll_number">Roll Number (Cannot be changed)</label>
                        <input type="text" id="roll_number" value="<?php echo htmlspecialchars($user['roll_number']); ?>" disabled>
                    </div>
                    
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="department">Department</label>
                        <select id="department" name="department">
                            <option value="">Select Department</option>
                            <option value="Computer Science" <?php echo $user['department'] == 'Computer Science' ? 'selected' : ''; ?>>Computer Science</option>
                            <option value="Information Technology" <?php echo $user['department'] == 'Information Technology' ? 'selected' : ''; ?>>Information Technology</option>
                            <option value="Electronics" <?php echo $user['department'] == 'Electronics' ? 'selected' : ''; ?>>Electronics</option>
                            <option value="Mechanical" <?php echo $user['department'] == 'Mechanical' ? 'selected' : ''; ?>>Mechanical</option>
                            <option value="Civil" <?php echo $user['department'] == 'Civil' ? 'selected' : ''; ?>>Civil</option>
                            <option value="Electrical" <?php echo $user['department'] == 'Electrical' ? 'selected' : ''; ?>>Electrical</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="graduation_year">Graduation Year</label>
                        <input type="number" id="graduation_year" name="graduation_year" value="<?php echo htmlspecialchars($user['graduation_year']); ?>" min="2020" max="2030">
                    </div>
                    
                    <div class="form-group">
                        <label for="cgpa">CGPA</label>
                        <input type="number" id="cgpa" name="cgpa" step="0.01" min="0" max="10" value="<?php echo htmlspecialchars($user['cgpa']); ?>">
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Update Profile</button>
                    <a href="student_dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
                </form>
            </div>
        </div>
    </div>
</body>
</html>