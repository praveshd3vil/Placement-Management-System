<?php
require_once 'config.php';
require_admin();

$success = '';

// Handle status update
if (isset($_POST['update_status'])) {
    $app_id = (int)$_POST['app_id'];
    $new_status = clean_input($_POST['status']);
    
    $sql = "UPDATE applications SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $new_status, $app_id);
    
    if ($stmt->execute()) {
        $success = 'Application status updated successfully!';
    }
}

// Get all applications
$apps_sql = "SELECT a.*, u.name as student_name, u.email as student_email, u.phone as student_phone,
             u.roll_number, u.department, u.cgpa, jp.title as job_title, c.name as company_name 
             FROM applications a 
             LEFT JOIN users u ON a.user_id = u.id 
             LEFT JOIN job_postings jp ON a.job_id = jp.id 
             LEFT JOIN companies c ON jp.company_id = c.id 
             ORDER BY a.applied_at DESC";
$apps = $conn->query($apps_sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Applications - Placement System</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        
        .modal-content {
            background-color: white;
            margin: 5% auto;
            padding: 30px;
            border-radius: 10px;
            width: 80%;
            max-width: 600px;
            max-height: 80vh;
            overflow-y: auto;
        }
        
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        
        .close:hover {
            color: #000;
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
                <h1>Manage Applications</h1>
            </div>

            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>

            <div class="card">
                <h3>All Applications</h3>
                <?php if ($apps->num_rows > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>Roll No</th>
                                <th>Department</th>
                                <th>CGPA</th>
                                <th>Company</th>
                                <th>Job Title</th>
                                <th>Applied On</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($app = $apps->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($app['student_name']); ?></td>
                                    <td><?php echo htmlspecialchars($app['roll_number']); ?></td>
                                    <td><?php echo htmlspecialchars($app['department']); ?></td>
                                    <td><?php echo htmlspecialchars($app['cgpa']); ?></td>
                                    <td><?php echo htmlspecialchars($app['company_name']); ?></td>
                                    <td><?php echo htmlspecialchars($app['job_title']); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($app['applied_at'])); ?></td>
                                    <td>
                                        <span class="badge badge-<?php echo strtolower($app['status']); ?>">
                                            <?php echo $app['status']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <button onclick="viewApplication(<?php echo htmlspecialchars(json_encode($app)); ?>)" class="btn btn-info" style="padding: 5px 10px; font-size: 0.85rem;">View</button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No applications yet.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div id="appModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <div id="modalBody"></div>
        </div>
    </div>

    <script>
        function viewApplication(app) {
            const modal = document.getElementById('appModal');
            const modalBody = document.getElementById('modalBody');
            
            modalBody.innerHTML = `
                <h2>Application Details</h2>
                <div style="margin: 20px 0;">
                    <h3>Student Information</h3>
                    <p><strong>Name:</strong> ${app.student_name}</p>
                    <p><strong>Roll Number:</strong> ${app.roll_number}</p>
                    <p><strong>Email:</strong> ${app.student_email}</p>
                    <p><strong>Phone:</strong> ${app.student_phone}</p>
                    <p><strong>Department:</strong> ${app.department}</p>
                    <p><strong>CGPA:</strong> ${app.cgpa}</p>
                </div>
                
                <div style="margin: 20px 0;">
                    <h3>Job Information</h3>
                    <p><strong>Company:</strong> ${app.company_name}</p>
                    <p><strong>Job Title:</strong> ${app.job_title}</p>
                    <p><strong>Applied On:</strong> ${new Date(app.applied_at).toLocaleDateString()}</p>
                </div>
                
                <div style="margin: 20px 0;">
                    <h3>Cover Letter</h3>
                    <p style="white-space: pre-wrap;">${app.cover_letter || 'No cover letter provided'}</p>
                </div>
                
                <form method="POST" action="">
                    <input type="hidden" name="app_id" value="${app.id}">
                    <div class="form-group">
                        <label for="status">Update Status</label>
                        <select id="status" name="status" class="form-control">
                            <option value="Pending" ${app.status === 'Pending' ? 'selected' : ''}>Pending</option>
                            <option value="Shortlisted" ${app.status === 'Shortlisted' ? 'selected' : ''}>Shortlisted</option>
                            <option value="Selected" ${app.status === 'Selected' ? 'selected' : ''}>Selected</option>
                            <option value="Rejected" ${app.status === 'Rejected' ? 'selected' : ''}>Rejected</option>
                        </select>
                    </div>
                    <button type="submit" name="update_status" class="btn btn-primary">Update Status</button>
                </form>
            `;
            
            modal.style.display = 'block';
        }
        
        function closeModal() {
            document.getElementById('appModal').style.display = 'none';
        }
        
        window.onclick = function(event) {
            const modal = document.getElementById('appModal');
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }
    </script>
</body>
</html>