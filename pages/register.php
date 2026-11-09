<?php
require_once __DIR__ . '/../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $budget = $_POST['budget'];

    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);

    if ($stmt->fetch()) {
        $error = "Username already exists.";
    } else {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (username, password, monthly_budget) VALUES (?, ?, ?)");
        $stmt->execute([$username, $hashed, $budget]);
        header('Location: index.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - Expense Tracker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container d-flex align-items-center justify-content-center" style="height: 100vh;">
        <div class="card p-4 shadow-sm w-100" style="max-width: 400px;">
            <h3 class="mb-3 text-center">Register</h3>
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>
            <form method="post">
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input required type="text" name="username" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input required type="password" name="password" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">Monthly Budget</label>
                    <input required type="number" step="0.01" name="budget" class="form-control">
                </div>
                <button type="submit" class="btn btn-success w-100">Register</button>
                <div class="text-center mt-3">
                    <small>Already have an account? <a href="index.php">Login</a></small>
                </div>
            </form>
        </div>
    </div>
</body>
</html>