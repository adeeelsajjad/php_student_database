<?php
require_once 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$id = (int) ($_POST['id'] ?? 0);
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if (!$id || empty($name) || empty($email)) {
    header("Location: edit.php?id=$id&error=1");
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: edit.php?id=$id&error=1");
    exit;
}

try {
    if (!empty($password)) {
        if (strlen($password) < 6) {
            header("Location: edit.php?id=$id&error=1");
            exit;
        }
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, password = ? WHERE id = ?");
        $stmt->execute([$name, $email, $hashedPassword, $id]);
    } else {
        $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
        $stmt->execute([$name, $email, $id]);
    }
    header('Location: index.php?updated=1');
} catch (PDOException $e) {
    if ($e->getCode() == 23000) {
        header("Location: edit.php?id=$id&error=1");
    } else {
        die("Error: " . $e->getMessage());
    }
}
exit;
