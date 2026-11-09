<?php
require_once __DIR__ . '/config.php';

// Fetch all expenses
function getExpenses($user_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM expenses WHERE user_id = :user_id ORDER BY created_at DESC");
    $stmt->execute(['user_id' => $user_id]);
    return $stmt->fetchAll();
}

// Add a new expense
function addExpense($user_id, $amount, $category, $date, $notes) {
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO expenses (user_id, amount, category, date, notes, created_at) VALUES (:user_id, :amount, :category, :date, :notes, NOW())");
    $stmt->execute([
        'user_id' => $user_id,
        'amount' => $amount,
        'category' => $category,
        'date' => $date,
        'notes' => $notes
    ]);
}

// Get single expense
function getExpenseById($id, $user_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM expenses WHERE id = :id AND user_id = :user_id");
    $stmt->execute(['id' => $id, 'user_id' => $user_id]);
    return $stmt->fetch();
}

// Update an expense
function updateExpense($id, $user_id, $amount, $category, $date, $notes) {
    global $pdo;
    $stmt = $pdo->prepare("UPDATE expenses SET amount = :amount, category = :category, date = :date, notes = :notes WHERE id = :id AND user_id = :user_id");
    $stmt->execute([
        'amount' => $amount,
        'category' => $category,
        'date' => $date,
        'notes' => $notes,
        'id' => $id,
        'user_id' => $user_id
    ]);
}

// Delete an expense
function deleteExpense($id, $user_id) {
    global $pdo;
    $stmt = $pdo->prepare("DELETE FROM expenses WHERE id = :id AND user_id = :user_id");
    $stmt->execute([
        'id' => $id,
        'user_id' => $user_id
    ]);
}

// Get daily total spending
function getDailyTotal($user_id) {
    global $pdo;
    $today = date('Y-m-d');
    $stmt = $pdo->prepare("SELECT SUM(amount) AS total FROM expenses WHERE user_id = :user_id AND date = :today");
    $stmt->execute(['user_id' => $user_id, 'today' => $today]);
    $result = $stmt->fetch();
    return $result['total'] ?? 0;
}

// Get monthly total spending
function getMonthlyTotal($user_id) {
    global $pdo;
    $month = date('Y-m');
    $stmt = $pdo->prepare("SELECT SUM(amount) AS total FROM expenses WHERE user_id = :user_id AND DATE_FORMAT(date, '%Y-%m') = :month");
    $stmt->execute(['user_id' => $user_id, 'month' => $month]);
    $result = $stmt->fetch();
    return $result['total'] ?? 0;
}

// Get yearly total spending
function getYearlyTotal($user_id) {
    global $pdo;
    $year = date('Y');
    $stmt = $pdo->prepare("SELECT SUM(amount) AS total FROM expenses WHERE user_id = :user_id AND YEAR(date) = :year");
    $stmt->execute(['user_id' => $user_id, 'year' => $year]);
    $result = $stmt->fetch();
    return $result['total'] ?? 0;
}

// Prepare data for the expense distribution chart
function prepareChartData($user_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT category, SUM(amount) AS total FROM expenses WHERE user_id = :user_id GROUP BY category");
    $stmt->execute(['user_id' => $user_id]);
    $data = $stmt->fetchAll();

    $labels = [];
    $values = [];

    foreach ($data as $row) {
        $labels[] = $row['category'];
        $values[] = $row['total'];
    }

    return ['labels' => $labels, 'values' => $values];
}

// Get the user's monthly budget
function getUserMonthlyBudget($user_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT monthly_budget FROM users WHERE id = :id");
    $stmt->execute(['id' => $user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    return $user['monthly_budget'] ?? 0;
}

// Update the user's monthly budget
function updateMonthlyBudget($user_id, $monthly_budget) {
    global $pdo;
    $stmt = $pdo->prepare("UPDATE users SET monthly_budget = :monthly_budget WHERE id = :id");
    $stmt->execute(['monthly_budget' => $monthly_budget, 'id' => $user_id]);
}

// Get category budgets for a user
function getCategoryBudgets($user_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT category, budget FROM category_budgets WHERE user_id = :user_id");
    $stmt->execute(['user_id' => $user_id]);
    $budgets = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $budgets[$row['category']] = $row['budget'];
    }
    return $budgets;
}

// Update category budgets for a user
function updateCategoryBudgets($user_id, $categoryBudgets) {
    global $pdo;
    foreach ($categoryBudgets as $category => $budget) {
        $stmt = $pdo->prepare("INSERT INTO category_budgets (user_id, category, budget) 
            VALUES (:user_id, :category, :budget)
            ON DUPLICATE KEY UPDATE budget = :budget");
        $stmt->execute([
            'user_id' => $user_id,
            'category' => $category,
            'budget' => $budget
        ]);
    }
}

// Get spending per category for the current month
function getCategorySpending($user_id) {
    global $pdo;
    $currentMonth = date('Y-m');
    $stmt = $pdo->prepare("SELECT category, SUM(amount) AS total FROM expenses WHERE user_id = :user_id AND DATE_FORMAT(date, '%Y-%m') = :currentMonth GROUP BY category");
    $stmt->execute(['user_id' => $user_id, 'currentMonth' => $currentMonth]);
    $spending = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $spending[$row['category']] = $row['total'];
    }
    return $spending;
}

// Reset category warnings for the user
function resetCategoryWarnings($user_id) {
    global $pdo;
    $query = "UPDATE category_budgets SET warning = 0 WHERE user_id = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$user_id]);
}

// Get the last reset month for the user
function getLastResetMonth($user_id) {
    global $pdo; // Using $pdo as the database connection
    $query = "SELECT last_reset_month FROM users WHERE id = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$user_id]);
    return $stmt->fetchColumn(); // Returns the last reset month (e.g., '2025-04')
}

// Update the last reset month for the user
function updateLastResetMonth($user_id, $currentMonth) {
    global $pdo; // Ensure you have access to the PDO instance
    $query = "UPDATE users SET last_reset_month = ? WHERE id = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$currentMonth, $user_id]);
}
?>
