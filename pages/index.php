<?php
session_start();
require_once __DIR__ . '/../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_budget'] = $user['monthly_budget'];
        header('Location: dashboard.php');
        exit;
    } else {
        $error = "Invalid username or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Expense Tracker</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="flex items-center justify-center h-screen">
        <div class="bg-white shadow-lg rounded-lg p-8 max-w-sm w-full">
            <h2 class="text-2xl font-bold text-center text-blue-600 mb-6">Expense Tracker</h2>
            <?php if (isset($error)): ?>
                <div class="bg-red-100 text-red-700 p-3 rounded mb-4"><?= $error ?></div>
            <?php endif; ?>
            <form method="post">
                <div class="mb-4">
                    <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                    <input type="text" name="username" id="username" class="w-full border rounded px-3 py-2" required>
                </div>
                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <input type="password" name="password" id="password" class="w-full border rounded px-3 py-2" required>
                </div>
                <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition">Login</button>
            </form>
            <div class="text-center mt-4">
                <p class="text-sm text-gray-600">Don't have an account? <a href="register.php" class="text-blue-600 hover:underline">Register</a></p>
            </div>
        </div>
    </div>
</body>
</html>