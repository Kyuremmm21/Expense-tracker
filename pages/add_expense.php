<?php
session_start();
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = $_POST['amount'];
    $category = $_POST['category'];
    $date = $_POST['date'];  
    $notes = $_POST['notes'] ?? ''; 

    addExpense($user_id, $amount, $category, $date, $notes);
    header('Location: dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Expense</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <nav class="bg-blue-600 shadow-md">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center">
            <div class="text-lg font-bold text-white">Expense Tracker</div>
            <div class="space-x-4">
                <a href="dashboard.php" class="text-white hover:text-blue-200">Dashboard</a>
                <a href="about.php" class="text-white hover:text-blue-200">About</a>
                <a href="logout.php" class="text-red-200 hover:text-red-400">Logout</a>
            </div>
        </div>
    </nav>

    <div class="max-w-xl mx-auto mt-10 bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-2xl font-bold mb-4 text-center text-blue-600">Add New Expense</h2>

        <form action="add_expense.php" method="POST">
            <div class="mb-4">
                <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">Amount (₱)</label>
                <input type="number" name="amount" step="0.01" min="0" class="w-full border rounded px-3 py-2" required>
            </div>

            <div class="mb-4">
                <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                <select name="category" class="w-full border rounded px-3 py-2" required>
                    <option value="Food">Food</option>
                    <option value="Transport">Transport</option>
                    <option value="Entertainment">Entertainment</option>
                    <option value="Utilities">Utilities</option>
                    <option value="Others">Others</option>
                </select>
            </div>

            <div class="mb-4">
                <label for="date" class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                <input type="date" name="date" class="w-full border rounded px-3 py-2" required>
            </div>

            <div class="mb-4">
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                <textarea name="notes" class="w-full border rounded px-3 py-2" rows="3"></textarea>
            </div>

            <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">Add Expense</button>
        </form>
    </div>
</body>
</html>