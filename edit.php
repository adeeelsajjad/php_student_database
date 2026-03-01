<?php
require_once 'config/database.php';

$id = (int) ($_GET['id'] ?? 0);
if (!$id) {
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare("SELECT id, name, email FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();

if (!$user) {
    header('Location: index.php');
    exit;
}

$message = '';
$messageType = '';
if (isset($_GET['error'])) {
    $message = 'Please fill all fields correctly.';
    $messageType = 'error';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User - <?= htmlspecialchars($user['name']) ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Edit User</h1>
            <p>Update user information</p>
        </header>

        <?php if ($message): ?>
            <div class="alert alert-<?= $messageType ?>"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <div class="card">
            <h2>Edit <?= htmlspecialchars($user['name']) ?></h2>
            <form action="update.php" method="POST" class="edit-form" onsubmit="return confirm('Are you sure you want to update this user?');">
                <input type="hidden" name="id" value="<?= $user['id'] ?>">
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" id="name" name="name" required 
                           value="<?= htmlspecialchars($user['name']) ?>">
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required 
                           value="<?= htmlspecialchars($user['email']) ?>">
                </div>
                <div class="form-group">
                    <label for="password">New Password <span style="color: var(--text-muted); font-weight: normal;">(leave blank to keep current)</span></label>
                    <input type="password" id="password" name="password" placeholder="••••••••">
                </div>
                <div class="btn-group">
                    <button type="submit" class="btn btn-primary">Update User</button>
                    <a href="index.php" class="btn" style="background: var(--bg-card); color: var(--text-primary);">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
