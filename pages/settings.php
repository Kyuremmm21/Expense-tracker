<?php
session_start();
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$current_monthly_budget = getUserMonthlyBudget($user_id);
$categoryBudgets = getCategoryBudgets($user_id);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['action'] === 'update_category_budgets' && isset($_POST['budgets'])) {
        try {
            $budgets = array_map('floatval', $_POST['budgets']);
            $totalCategoryBudgets = array_sum($budgets);

            // Validate that the total category budgets do not exceed the monthly budget
            if ($totalCategoryBudgets > $current_monthly_budget) {
                throw new Exception("The total of category budgets (₱" . number_format($totalCategoryBudgets, 2) . ") cannot exceed the monthly budget (₱" . number_format($current_monthly_budget, 2) . ").");
            }

            updateCategoryBudgets($user_id, $budgets);
            header("Location: settings.php?success=1");
        } catch (Exception $e) {
            header("Location: settings.php?error=" . urlencode($e->getMessage()));
        }
        exit;
    }

    if ($_POST['action'] === 'update_monthly_budget' && isset($_POST['monthly_budget'])) {
        try {
            $newMonthlyBudget = floatval($_POST['monthly_budget']);
            $totalCategoryBudgets = array_sum($categoryBudgets);

            // Validate that the new monthly budget is not less than the total category budgets
            if ($newMonthlyBudget < $totalCategoryBudgets) {
                throw new Exception("The monthly budget (₱" . number_format($newMonthlyBudget, 2) . ") cannot be less than the total of category budgets (₱" . number_format($totalCategoryBudgets, 2) . ").");
            }

            updateMonthlyBudget($user_id, $newMonthlyBudget);
            header("Location: settings.php?success=1");
        } catch (Exception $e) {
            header("Location: settings.php?error=" . urlencode($e->getMessage()));
        }
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Settings - Expense Tracker</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <nav class="bg-white shadow-md">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center">
            <div class="text-lg font-bold text-blue-600">Expense Tracker</div>
            <div class="space-x-4">
                <a href="dashboard.php" class="text-gray-700 hover:text-blue-600">Dashboard</a>
                <a href="about.php" class="text-gray-700 hover:text-blue-600">About</a>
                <a href="logout.php" class="text-red-600 hover:text-red-800">Logout</a>
            </div>
        </div>
    </nav>

    <div class="max-w-xl mx-auto mt-10 bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-2xl font-bold mb-4 text-center">Settings</h2>

        <?php if (isset($_GET['success'])): ?>
            <div class="mb-4 p-3 text-green-700 bg-green-100 rounded">Settings updated successfully!</div>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
            <div class="mb-4 p-3 text-red-700 bg-red-100 rounded"><?= htmlspecialchars($_GET['error']) ?></div>
        <?php endif; ?>

        <!-- Update Monthly Budget -->
        <form action="settings.php" method="POST" class="mb-6">
            <input type="hidden" name="action" value="update_monthly_budget">
            <div class="mb-4">
                <label for="monthly_budget" class="block text-sm font-medium text-gray-700 mb-1">Monthly Budget (₱)</label>
                <input type="number" name="monthly_budget" step="0.01" min="0" class="w-full border rounded px-3 py-2" value="<?= htmlspecialchars($current_monthly_budget) ?>" required>
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">Update Monthly Budget</button>
        </form>

        <!-- Update Category Budgets -->
        <h3 class="text-lg font-bold mb-4">Set Category Budgets</h3>
        <form action="settings.php" method="POST">
            <input type="hidden" name="action" value="update_category_budgets">

            <?php foreach (['Food', 'Transport', 'Entertainment', 'Utilities', 'Others'] as $category): ?>
                <div class="mb-4">
                    <label for="budget_<?= $category ?>" class="block text-sm font-medium text-gray-700">
                        <?= $category ?> Budget (₱)
                    </label>
                    <input type="number" name="budgets[<?= $category ?>]" id="budget_<?= $category ?>" 
                           class="w-full border rounded px-3 py-2" 
                           value="<?= htmlspecialchars($categoryBudgets[$category] ?? 0) ?>" 
                           step="0.01" min="0" required>
                </div>
            <?php endforeach; ?>

            <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                Update Category Budgets
            </button>
        </form>
    </div>
</body>
</html>
