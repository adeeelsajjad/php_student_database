<?php
require_once 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

$errors = [];

if (empty($name)) {
    $errors[] = 'Name is required.';
}
if (empty($email)) {
    $errors[] = 'Email is required.';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Invalid email format.';
}
if (empty($password)) {
    $errors[] = 'Password is required.';
} elseif (strlen($password) < 6) {
    $errors[] = 'Password must be at least 6 characters.';
}

if (!empty($errors)) {
    session_start();
    $_SESSION['errors'] = $errors;
    $_SESSION['old'] = ['name' => $name, 'email' => $email];
    header('Location: index.php');
    exit;
}

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

try {
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
    $stmt->execute([$name, $email, $hashedPassword]);
    header('Location: index.php?added=1');
} catch (PDOException $e) {
    if ($e->getCode() == 23000) {
        session_start();
        $_SESSION['errors'] = ['Email already exists.'];
        $_SESSION['old'] = ['name' => $name, 'email' => $email];
        header('Location: index.php');
    } else {
        die("Error: " . $e->getMessage());
    }
}
exit;
