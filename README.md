# Placement-Management-System

A comprehensive web-based placement management system built with PHP and MySQL to streamline the campus recruitment process. This system connects students with job opportunities and provides administrators with tools to manage the entire placement cycle.

## 🌟 Features

### For Students
- **User Registration & Login**: Secure authentication system for student accounts
- **Profile Management**: Update personal information, academic details, and contact information
- **Job Browsing**: View all active job postings from various companies
- **Easy Application**: Apply to jobs with custom cover letters
- **Application Tracking**: Monitor application status in real-time (Pending, Shortlisted, Selected, Rejected)
- **Dashboard**: Centralized view of applications and available opportunities

### For Administrators
- **Secure Admin Panel**: Protected admin login with password management
- **Student Management**: View, manage, and monitor all registered students
- **Company Management**: Add, edit, and delete company profiles
- **Job Posting Management**: Create, update, and manage job postings
- **Application Review**: View detailed applications and update their status
- **Analytics Dashboard**: Quick overview of total students, companies, jobs, and applications
- **Password Security**: Built-in password change functionality

## 🛠️ Technologies Used

- **Backend**: PHP 7.4+
- **Database**: MySQL
- **Frontend**: HTML5, CSS3, JavaScript
- **Design**: Custom CSS with responsive grid layout
- **Security**: Password hashing, prepared statements, input sanitization

## 📋 Prerequisites

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache/Nginx)
- phpMyAdmin (optional, for database management)

## 🚀 Installation

### 1. Clone or Download the Project

```bash
git clone <repository-url>
cd placement-management-system
```

### 2. Database Setup

1. Create a new MySQL database:
```sql
CREATE DATABASE placement_system;
```

2. Import the database schema:
```sql
USE placement_system;

-- Users table
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    roll_number VARCHAR(50) UNIQUE NOT NULL,
    department VARCHAR(100),
    graduation_year INT,
    cgpa DECIMAL(3,2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Admins table
CREATE TABLE admins (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(100) UNIQUE NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Companies table
CREATE TABLE companies (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    website VARCHAR(255),
    contact_person VARCHAR(255),
    contact_email VARCHAR(255),
    contact_phone VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Job postings table
CREATE TABLE job_postings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    company_id INT,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    requirements TEXT,
    salary_range VARCHAR(100),
    location VARCHAR(255),
    job_type ENUM('Full-time', 'Internship', 'Part-time') DEFAULT 'Full-time',
    application_deadline DATE,
    status ENUM('Active', 'Closed') DEFAULT 'Active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE
);

-- Applications table
CREATE TABLE applications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    job_id INT,
    cover_letter TEXT,
    status ENUM('Pending', 'Shortlisted', 'Selected', 'Rejected') DEFAULT 'Pending',
    applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (job_id) REFERENCES job_postings(id) ON DELETE CASCADE
);
```

3. Create default admin account:
```sql
INSERT INTO admins (username, email, password) 
VALUES ('admin', 'admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');
-- Default password: password (please change after first login)
```

### 3. Configure Database Connection

Edit `config.php` with your database credentials:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'your_db_username');
define('DB_PASS', 'your_db_password');
define('DB_NAME', 'placement_system');
```

### 4. Deploy to Web Server

1. Copy all project files to your web server's document root
2. Ensure proper file permissions
3. Access the application through your web browser

## 🔐 Default Login Credentials

### Administrator
- **Username**: admin
- **Password**: admin@123
- **Note**: Change this password immediately after first login using the "Change Password" feature

### Student
Students need to register first using the registration form.

## 📁 Project Structure

```
placement-management-system/
├── css/
│   └── style.css              # Main stylesheet
├── config.php                 # Database configuration
├── index.php                  # Landing page
├── login.php                  # Student login
├── register.php               # Student registration
├── logout.php                 # Logout functionality
├── student_dashboard.php      # Student dashboard
├── student_profile.php        # Student profile management
├── apply_job.php              # Job application form
├── admin_login.php            # Admin login
├── admin_dashboard.php        # Admin dashboard
├── change_password.php        # Admin password change
├── manage_students.php        # Student management
├── manage_companies.php       # Company management
├── manage_jobs.php            # Job posting management
├── manage_applications.php    # Application management
├── edit_company.php           # Company editing
├── edit_job.php               # Job editing
├── view_student.php           # Student details view
├── view_application.php       # Application details view
└── README.md                  # This file
```

## 🎯 Usage Guide

### For Students

1. **Registration**: Navigate to the registration page and fill in your details
2. **Login**: Use your registered email and password to log in
3. **Update Profile**: Keep your profile information up-to-date
4. **Browse Jobs**: View available job opportunities on your dashboard
5. **Apply**: Click "Apply Now" on any job and submit your application with a cover letter
6. **Track Applications**: Monitor your application status from the dashboard

### For Administrators

1. **Login**: Access the admin panel with your credentials
2. **Manage Students**: View all registered students and their details
3. **Add Companies**: Register companies that will be recruiting
4. **Post Jobs**: Create job postings linked to companies
5. **Review Applications**: View detailed application information
6. **Update Status**: Change application status (Pending → Shortlisted → Selected/Rejected)
7. **Analytics**: Monitor placement statistics from the dashboard

## 🔒 Security Features

- Password hashing using PHP's `password_hash()`
- SQL injection prevention using prepared statements
- Input sanitization and validation
- Session-based authentication
- CSRF protection through form validation
- Role-based access control (Student/Admin)

## 🎨 UI/UX Features

- Responsive design for mobile and desktop
- Modern gradient color scheme
- Intuitive navigation
- Status badges for applications
- Interactive forms with validation
- Clean table layouts for data display
- Modal popups for detailed views

## 🐛 Troubleshooting

### Database Connection Issues
- Verify database credentials in `config.php`
- Ensure MySQL service is running
- Check database user permissions

### Login Problems
- Clear browser cookies and session data
- Verify correct credentials
- Check if user exists in database

### File Upload Issues
- Check PHP upload settings in `php.ini`
- Verify folder permissions

## 🔄 Future Enhancements

- Email notifications for application status updates
- Resume upload functionality
- Advanced search and filter options
- Interview scheduling system
- Bulk email functionality
- Export reports to PDF/Excel
- Student skill assessment module
- Company ratings and reviews

## 👥 Authors

- **Tushar Deb**
- **Pravesh Burathoki**

## 📄 License

This project is for educational purposes. Feel free to modify and use it for your institution.

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## 📞 Support

For support or queries, please contact the development team.

---

**Note**: This is an academic project designed for college placement management. Ensure proper testing before deploying in a production environment.
