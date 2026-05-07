<?php
$host = 'localhost';
$dbname = 'gctu_library';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Clear existing users to prevent duplicates if script is run multiple times
    $pdo->exec("DELETE FROM users");
    $pdo->exec("ALTER TABLE users AUTO_INCREMENT = 1");

    // Seed Admin
    $admin_hash = password_hash('admin123', PASSWORD_BCRYPT);
    $stmt = $pdo->prepare("INSERT INTO users (username, password_hash, email, role, department_id, status) VALUES (?, ?, ?, 'admin', 1, 'active')");
    $stmt->execute(['admin', $admin_hash, 'admin@gctu.edu.gh']);

    // Seed Student
    $student_hash = password_hash('student123', PASSWORD_BCRYPT);
    $stmt = $pdo->prepare("INSERT INTO users (username, password_hash, email, role, department_id, status) VALUES (?, ?, ?, 'student', 2, 'active')");
    $stmt->execute(['student', $student_hash, 'student@gctu.edu.gh']);

    // Seed some mock projects into the real DB
    $pdo->exec("DELETE FROM projects");
    $pdo->exec("ALTER TABLE projects AUTO_INCREMENT = 1");
    
    $pdo->exec("INSERT INTO projects (title, abstract, keywords, category_id, department_id, file_path, upload_date, uploader_id, approval_status, view_count) VALUES 
        ('Secure Cloud Storage for SMEs in Ghana', 'This study explores the implementation of robust encryption algorithms...', 'Cloud, Security, SMEs', 3, 1, 'uploads/sample.pdf', '2024-03-15', 2, 'approved', 145),
        ('AI-Powered Chatbot for Student Services', 'A project focusing on the development of a natural language processing system...', 'AI, Chatbot, Education', 1, 2, 'uploads/sample.pdf', '2024-04-10', 2, 'approved', 320),
        ('Mobile Health App for Maternal Care', 'A cross-platform mobile application designed to connect rural pregnant women with healthcare providers.', 'mHealth, Maternal Care', 2, 2, 'uploads/sample.pdf', '2024-02-14', 2, 'pending', 198)
    ");
    
    echo "Database seeded successfully!\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
