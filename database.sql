-- NOTE: Removed CREATE DATABASE / USE statements so import targets the
-- already-provisioned database on shared hosts. Select the target
-- database in phpMyAdmin before importing this file.

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    role ENUM('student', 'admin') NOT NULL DEFAULT 'student',
    created_at DATETIME NOT NULL
);

CREATE TABLE IF NOT EXISTS otps (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    code VARCHAR(6) NOT NULL,
    expires_at DATETIME NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    created_at DATETIME NOT NULL
);

CREATE TABLE IF NOT EXISTS jobs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    company VARCHAR(255) NOT NULL,
    location VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    posted_at DATETIME NOT NULL
);

CREATE TABLE IF NOT EXISTS enrollments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    course_id INT NOT NULL,
    enrolled_at DATETIME NOT NULL,
    UNIQUE KEY unique_enrollment (user_id, course_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS applications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    job_id INT NOT NULL,
    applied_at DATETIME NOT NULL,
    status ENUM('pending', 'reviewed', 'rejected', 'hired') NOT NULL DEFAULT 'pending',
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (job_id) REFERENCES jobs(id) ON DELETE CASCADE
);

INSERT IGNORE INTO users (email, role, created_at) VALUES
('admin@capstone.local', 'admin', NOW()),
('student1@capstone.local', 'student', NOW()),
('student2@capstone.local', 'student', NOW()),
('learner1@capstone.local', 'student', NOW()),
('learner2@capstone.local', 'student', NOW());

INSERT IGNORE INTO courses (title, description, price, created_at) VALUES
('Full-Stack Web Development', 'Build modern web applications with PHP, MySQL, and JavaScript.', 249.00, NOW()),
('Data Analytics with Python', 'Learn data analysis, visualization, and reporting with Python.', 199.00, NOW()),
('UI/UX Design Bootcamp', 'Create intuitive user experiences and polished visual design systems.', 179.00, NOW()),
('Cloud Computing Fundamentals', 'Master AWS, deployment workflows, and infrastructure scaling.', 229.00, NOW()),
('Digital Marketing Mastery', 'Plan campaigns, analyze metrics, and optimize conversion funnels.', 159.00, NOW()),
('Cybersecurity Essentials', 'Protect applications by learning network security and incident response.', 189.00, NOW());

INSERT IGNORE INTO jobs (title, company, location, description, posted_at) VALUES
('Junior Web Developer', 'Tech Solutions', 'Remote', 'Support web app development and maintenance with PHP and JavaScript.', NOW()),
('Data Analyst Intern', 'Analytics Co', 'Onsite', 'Assist with data modeling, reporting, and dashboard creation.', NOW()),
('Front-End Engineer', 'Design Labs', 'Hybrid', 'Develop responsive interfaces and collaborate with design teams.', NOW()),
('QA Engineer', 'QualityWorks', 'Remote', 'Execute test plans and ensure product quality across releases.', NOW()),
('Product Manager', 'Innovate Hub', 'Hybrid', 'Coordinate teams, roadmap planning, and product delivery.', NOW());

INSERT IGNORE INTO enrollments (user_id, course_id, enrolled_at)
SELECT u.id, c.id, NOW() FROM users u JOIN courses c ON u.email='student1@capstone.local' AND c.title='Full-Stack Web Development'
UNION ALL
SELECT u.id, c.id, NOW() FROM users u JOIN courses c ON u.email='student1@capstone.local' AND c.title='Data Analytics with Python'
UNION ALL
SELECT u.id, c.id, NOW() FROM users u JOIN courses c ON u.email='student2@capstone.local' AND c.title='UI/UX Design Bootcamp'
UNION ALL
SELECT u.id, c.id, NOW() FROM users u JOIN courses c ON u.email='student2@capstone.local' AND c.title='Cloud Computing Fundamentals'
UNION ALL
SELECT u.id, c.id, NOW() FROM users u JOIN courses c ON u.email='learner1@capstone.local' AND c.title='Digital Marketing Mastery'
UNION ALL
SELECT u.id, c.id, NOW() FROM users u JOIN courses c ON u.email='learner2@capstone.local' AND c.title='Cybersecurity Essentials';

INSERT IGNORE INTO applications (user_id, job_id, applied_at, status)
SELECT u.id, j.id, NOW(), 'pending' FROM users u JOIN jobs j ON u.email='student1@capstone.local' AND j.title='Junior Web Developer'
UNION ALL
SELECT u.id, j.id, NOW(), 'pending' FROM users u JOIN jobs j ON u.email='student1@capstone.local' AND j.title='QA Engineer'
UNION ALL
SELECT u.id, j.id, NOW(), 'pending' FROM users u JOIN jobs j ON u.email='student2@capstone.local' AND j.title='Data Analyst Intern'
UNION ALL
SELECT u.id, j.id, NOW(), 'pending' FROM users u JOIN jobs j ON u.email='learner1@capstone.local' AND j.title='Front-End Engineer'
UNION ALL
SELECT u.id, j.id, NOW(), 'pending' FROM users u JOIN jobs j ON u.email='learner2@capstone.local' AND j.title='Product Manager';
