<?php
require_once 'config.php';
require_admin();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $error = 'All fields are required';
    } elseif ($new_password !== $confirm_password) {
        $error = 'New passwords do not match';
    } elseif (strlen($new_password) < 8) {
        $error = 'New password must be at least 8 characters';
    } elseif ($current_password === $new_password) {
        $error = 'New password must be different from current password';
    } else {
        // Verify current password
        $sql = "SELECT password FROM admins WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $_SESSION['admin_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        $admin = $result->fetch_assoc();
        
        if (!password_verify($current_password, $admin['password'])) {
            $error = 'Current password is incorrect';
        } else {
            // Update password
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $update_sql = "UPDATE admins SET password = ? WHERE id = ?";
            $stmt = $conn->prepare($update_sql);
            $stmt->bind_param("si", $hashed_password, $_SESSION['admin_id']);
            
            if ($stmt->execute()) {
                $success = 'Password changed successfully!';
            } else {
                $error = 'Failed to update password';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password - Placement System</title>
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
                <li><a href="manage_jobs.php">Jobs</a></li>
                <li><a href="manage_applications.php">Applications</a></li>
                <li><a href="manage_admins.php">Admins</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="dashboard">
        <div class="container">
            <div class="card" style="max-width: 600px; margin: 0 auto;">
                <h2>Change Your Password</h2>
                <p style="color: #666; margin-bottom: 2rem;">
                    Logged in as: <strong><?php echo htmlspecialchars($_SESSION['admin_username']); ?></strong>
                </p>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <?php echo $success; ?>
                        <br><a href="admin_dashboard.php">Back to Dashboard</a>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="current_password">Current Password *</label>
                        <input type="password" id="current_password" name="current_password" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="new_password">New Password * (min 8 characters)</label>
                        <input type="password" id="new_password" name="new_password" required minlength="8">
                        <small style="color: #666;">Use a strong password with uppercase, lowercase, numbers, and special characters</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_password">Confirm New Password *</label>
                        <input type="password" id="confirm_password" name="confirm_password" required minlength="8">
                    </div>
                    
                    <div style="display: flex; gap: 1rem;">
                        <button type="submit" class="btn btn-primary">Change Password</button>
                        <a href="admin_dashboard.php" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Password strength indicator
        document.getElementById('new_password').addEventListener('input', function() {
            const password = this.value;
            const strength = document.getElementById('strength-indicator');
            
            if (!strength) {
                const indicator = document.createElement('div');
                indicator.id = 'strength-indicator';
                indicator.style.marginTop = '5px';
                indicator.style.fontSize = '0.9rem';
                this.parentElement.appendChild(indicator);
            }
            
            let score = 0;
            const strengthText = document.getElementById('strength-indicator');
            
            if (password.length >= 8) score++;
            if (password.length >= 12) score++;
            if (/[a-z]/.test(password) && /[A-Z]/.test(password)) score++;
            if (/\d/.test(password)) score++;
            if (/[^a-zA-Z0-9]/.test(password)) score++;
            
            if (score <= 2) {
                strengthText.textContent = '⚠️ Weak password';
                strengthText.style.color = '#dc3545';
            } else if (score <= 4) {
                strengthText.textContent = '✓ Medium strength';
                strengthText.style.color = '#ffc107';
            } else {
                strengthText.textContent = '✓✓ Strong password';
                strengthText.style.color = '#28a745';
            }
        });
    </script>
</body>
</html>