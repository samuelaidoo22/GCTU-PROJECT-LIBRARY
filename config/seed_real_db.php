<?php
$environment = getenv('APP_ENV') ?: 'development';
if ($environment === 'production') {
    exit("Refusing to run the development seeder in production.\n");
}

$host = 'localhost';
$dbname = 'gctu_library';
$username = 'root';
$password = '';
$adminPassword = getenv('SEED_ADMIN_PASSWORD');
$studentPassword = getenv('SEED_STUDENT_PASSWORD');

if ($adminPassword === false || $studentPassword === false || strlen($adminPassword) < 12 || strlen($studentPassword) < 12) {
    exit("Set SEED_ADMIN_PASSWORD and SEED_STUDENT_PASSWORD to values of at least 12 characters before running the seeder.\n");
}

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $pdo->exec("INSERT IGNORE INTO departments (department_name, department_code) VALUES
        ('Information Technology', 'IT'),
        ('Mobile and Pervasive Computing', 'MPC'),
        ('General Science', 'GS'),
        ('Electrical and Electronic Engineering', 'EEE'),
        ('Mechanical Engineering', 'ME'),
        ('Civil Engineering', 'CE'),
        ('Software Engineering', 'SE'),
        ('Information Systems', 'IS'),
        ('Cybersecurity', 'CYB'),
        ('Data Science', 'DS'),
        ('Business Administration', 'BA'),
        ('Digital Marketing', 'DMKT'),
        ('Electrical Engineering', 'EE'),
        ('Mathematics and Statistics', 'MATHS'),
        ('Computer Engineering', 'CENG'),
        ('Artificial Intelligence', 'AI'),
        ('Computer Science', 'CS'),
        ('Engineering', 'ENG')");

    $categories = [
        ['Artificial Intelligence', 'AI and Machine Learning projects'],
        ['Web Development', 'Web-based systems and applications'],
        ['Network Security', 'Cybersecurity and network infrastructure'],
        ['Data Science', 'Data analysis and visualization'],
        ['Software Engineering', 'Software design and development'],
    ];
    $findCategory = $pdo->prepare("SELECT category_id FROM categories WHERE category_name = ? LIMIT 1");
    $insertCategory = $pdo->prepare("INSERT INTO categories (category_name, description) VALUES (?, ?)");
    foreach ($categories as [$name, $description]) {
        $findCategory->execute([$name]);
        if (!$findCategory->fetchColumn()) {
            $insertCategory->execute([$name, $description]);
        }
    }

    $departmentIds = $pdo->query("SELECT department_code, department_id FROM departments")
        ->fetchAll(PDO::FETCH_KEY_PAIR);

    $users = [
        ['admin', 'admin@gctu.edu.gh', 'admin', $departmentIds['IT'] ?? null],
        ['student', 'student@gctu.edu.gh', 'student', $departmentIds['MPC'] ?? null],
    ];
    $findUser = $pdo->prepare("SELECT user_id FROM users WHERE username = ? OR email = ? LIMIT 1");
    $insertUser = $pdo->prepare("INSERT INTO users (username, password_hash, email, role, department_id, status) VALUES (?, ?, ?, ?, ?, 'active')");

    foreach ($users as [$username, $email, $role, $departmentId]) {
        $findUser->execute([$username, $email]);
        if (!$findUser->fetchColumn()) {
            $password = $role === 'admin' ? $adminPassword : $studentPassword;
            $insertUser->execute([$username, password_hash($password, PASSWORD_BCRYPT), $email, $role, $departmentId]);
        }
    }

    echo "Database prerequisites and development accounts are ready. Existing records were preserved.\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
