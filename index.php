<?php
session_start();
require_once 'config/database.php';

// Fetch all users
$stmt = $pdo->query("SELECT id, name, email, created_at FROM users ORDER BY created_at DESC");
$users = $stmt->fetchAll();

$message = '';
$messageType = '';
$old = ['name' => '', 'email' => ''];

if (isset($_SESSION['errors'])) {
    $message = implode(' ', $_SESSION['errors']);
    $messageType = 'error';
    $old = $_SESSION['old'] ?? $old;
    unset($_SESSION['errors'], $_SESSION['old']);
} elseif (isset($_GET['added']) && $_GET['added'] == '1') {
    $message = 'User added successfully!';
    $messageType = 'success';
}
if (isset($_GET['updated']) && $_GET['updated'] == '1') {
    $message = 'User updated successfully!';
    $messageType = 'success';
}
if (isset($_GET['deleted']) && $_GET['deleted'] == '1') {
    $message = 'User deleted successfully!';
    $messageType = 'success';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - CRUD</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>User Management</h1>
            <p>Add, edit, and manage users</p>
        </header>

        <?php if ($message): ?>
            <div class="alert alert-<?= $messageType ?>"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <!-- Add User Form -->
        <div class="card">
            <h2>Add New User</h2>
            <form action="add.php" method="POST" onsubmit="return confirm('Are you sure you want to add this user?');">
                <div class="form-row">
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" id="name" name="name" required placeholder="John Doe" value="<?= htmlspecialchars($old['name']) ?>">
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required placeholder="john@example.com" value="<?= htmlspecialchars($old['email']) ?>">
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required placeholder="••••••••">
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Add User</button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Users List -->
        <div class="card">
            <h2>Users List</h2>
            <?php if (empty($users)): ?>
                <div class="empty-state">
                    <p>No users yet. Add your first user above!</p>
                </div>
            <?php else: ?>
                <table class="users-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $index => $user): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td><?= htmlspecialchars($user['name']) ?></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td><?= date('M j, Y', strtotime($user['created_at'])) ?></td>
                            <td>
                                <div class="actions">
                                    <a href="edit.php?id=<?= $user['id'] ?>" class="btn btn-edit btn-sm">Edit</a>
                                    <a href="delete.php?id=<?= $user['id'] ?>" class="btn btn-delete btn-sm" 
                                       onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
