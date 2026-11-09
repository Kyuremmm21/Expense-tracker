<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

include __DIR__ . '/../includes/functions.php';


$expense_id = $_GET['id'] ?? null;
$user_id = $_SESSION['user_id'];

if (!$expense_id) {
    header("Location: dashboard.php");
    exit;
}


$expense = getExpenseById($expense_id, $user_id);


if (!$expense) {
    echo "Expense not found.";
    exit;
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $amount = $_POST['amount'];
    $category = $_POST['category'];
    $date = $_POST['date'];
    $notes = $_POST['notes'];

    
    updateExpense($expense_id, $user_id, $amount, $category, $date, $notes);

    
    header("Location: dashboard.php");
    exit;
}


$categories = ['Food', 'Transport', 'Entertainment', 'Utilities', 'Health', 'Shopping', 'Education', 'Other'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Expense</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white text-center">
                        <h2 class="h4 mb-0">Edit Expense</h2>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            
                            <div class="mb-3">
                                <label for="amount" class="form-label">Amount</label>
                                <input type="number" name="amount" id="amount" 
                                       value="<?= htmlspecialchars($expense['amount']) ?>" 
                                       class="form-control" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="category" class="form-label">Category</label>
                                <select name="category" id="category" class="form-select" required>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?= $cat ?>" <?= ($expense['category'] === $cat) ? 'selected' : '' ?>><?= $cat ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="date" class="form-label">Date</label>
                                <input type="date" name="date" id="date" 
                                       value="<?= htmlspecialchars($expense['date']) ?>" 
                                       class="form-control" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="notes" class="form-label">Notes</label>
                                <textarea name="notes" id="notes" 
                                          class="form-control"><?= htmlspecialchars($expense['notes']) ?></textarea>
                            </div>
                           
                            <div class="d-flex justify-content-between">
                                <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary">Update Expense</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>