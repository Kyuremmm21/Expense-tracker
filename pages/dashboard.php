<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

include __DIR__ . '/../includes/functions.php';
$user_id = $_SESSION['user_id'];

// Initialize $currentMonth before using it
$currentMonth = date('Y-m');

// Debugging in a hidden div
$lastResetMonth = getLastResetMonth($user_id);
echo '<div style="display: none;">';
echo "Last Reset Month: $lastResetMonth<br>";
echo "Current Month: $currentMonth<br>";
echo '</div>';

// Check if the current month is different from the last reset month
if ($lastResetMonth !== $currentMonth) {
    resetCategoryWarnings($user_id); // Reset warnings
    updateLastResetMonth($user_id, $currentMonth); // Update the last reset month
}

// Fetch data for the dashboard
$todayTotal = getDailyTotal($user_id);
$monthTotal = getMonthlyTotal($user_id);
$yearTotal = getYearlyTotal($user_id);
$expenses = getExpenses($user_id);
$chartData = prepareChartData($user_id);

$userBudget = getUserMonthlyBudget($user_id);
$totalSpent = $monthTotal; 
$budgetPercentage = ($userBudget > 0) ? min(($totalSpent / $userBudget) * 100, 100) : 0;

// Fetch category budgets and spending
$categoryBudgets = getCategoryBudgets($user_id);
$categorySpending = getCategorySpending($user_id);
$categoryWarnings = [];

// Check if spending exceeds the budget for any category
foreach ($categoryBudgets as $category => $budget) {
    $currentMonthSpending = isset($categorySpending[$category]) ? $categorySpending[$category] : 0;
    if ($budget > 0 && $currentMonthSpending > $budget) {
        $categoryWarnings[$category] = $currentMonthSpending - $budget;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Expense Tracker</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.1.2/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-100">
<nav class="bg-blue-600 shadow-lg">
        <div class="container mx-auto px-6 py-4 flex justify-between items-center">
            
            <div class="flex items-center space-x-2">
                <i class="bi bi-cash-stack text-2xl text-white"></i>
                <h1 class="text-xl font-bold text-white">Expense Tracker</h1>
            </div>

            
            <div class="md:hidden">
                <button id="menuToggle" class="text-white focus:outline-none">
                    <i class="bi bi-list text-2xl"></i>
                </button>
            </div>

            
            <div id="navLinks" class="hidden md:flex space-x-6">
                <a href="about.php" class="text-white hover:text-blue-200">About Us</a>
                <a href="settings.php" class="text-white hover:text-blue-200">Settings</a>
                <a href="logout.php" class="text-white hover:text-blue-200">Logout</a>
            </div>
        </div>

        
        <div id="mobileMenu" class="hidden md:hidden bg-blue-700">
            <a href="about.php" class="block text-white py-2 px-4 hover:bg-blue-800">About Us</a>
            <a href="settings.php" class="block text-white py-2 px-4 hover:bg-blue-800">Settings</a>
            <a href="logout.php" class="block text-white py-2 px-4 hover:bg-blue-800">Logout</a>
        </div>
    </nav>

    
    <div class="container mx-auto px-6 py-8">
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            
            <div class="bg-white rounded-lg shadow-lg p-6 flex items-center justify-between hover:shadow-xl transition duration-300 ease-in-out">
                <div>
                    <p class="text-gray-600">Today's Spending</p>
                    <h2 class="text-3xl font-bold text-green-600">₱<?= number_format($todayTotal, 2) ?></h2>
                </div>
                <i class="bi bi-calendar-day text-4xl text-green-500"></i>
            </div>

            
            <div class="bg-white rounded-lg shadow-lg p-6 flex items-center justify-between hover:shadow-xl transition duration-300 ease-in-out">
                <div>
                    <p class="text-gray-600">This Month</p>
                    <h2 class="text-3xl font-bold text-orange-600">₱<?= number_format($monthTotal, 2) ?></h2>
                </div>
                <i class="bi bi-calendar-month text-4xl text-orange-500"></i>
            </div>

            
            <div class="bg-white rounded-lg shadow-lg p-6 flex items-center justify-between hover:shadow-xl transition duration-300 ease-in-out">
                <div>
                    <p class="text-gray-600">This Year</p>
                    <h2 class="text-3xl font-bold text-purple-600">₱<?= number_format($yearTotal, 2) ?></h2>
                </div>
                <i class="bi bi-calendar text-4xl text-purple-500"></i>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-bold mb-4">Expense Distribution</h2>
                <canvas id="expenseChart" height="300"></canvas>
            </div>
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-bold mb-4">Budget Progress</h2>
                <div class="mb-2">
                    <div class="flex justify-between mb-1">
                        <span class="text-sm font-medium">Monthly Budget</span>
                        <span class="text-sm font-medium">₱<?= number_format($totalSpent, 2) ?> / ₱<?= number_format($userBudget, 2) ?></span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-4">
                        <div class="bg-blue-600 h-4 rounded-full" style="width: <?= $budgetPercentage ?>%"></div>
                    </div>
                </div>
                <?php if ($budgetPercentage >= 100): ?>
                    <div class="mt-4 p-3 text-red-700 bg-red-100 rounded">
                        Warning: You have reached or exceeded your monthly budget!
                    </div>
                <?php endif; ?>
                <?php if (!empty($categoryWarnings)): ?>
                    <div class="mt-4 p-3 text-red-700 bg-red-100 rounded">
                        <h3 class="font-bold mb-2">Category Budget Warnings</h3>
                        <ul class="list-disc pl-5">
                            <?php foreach ($categoryWarnings as $category => $excess): ?>
                                <li>
                                    <strong><?= htmlspecialchars($category) ?>:</strong> Exceeded by ₱<?= number_format($excess, 2) ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                <div class="mt-6">
                    <a href="add_expense.php" class="w-full bg-blue-600 text-white py-2 rounded-lg text-center mb-4">+ Add Expense</a>
                    <h3 class="text-lg font-bold mb-2">Report</h3>
                    <form method="POST" enctype="multipart/form-data" class="space-y-2">
                        <button type="submit" name="export_csv" class="w-full bg-green-600 text-white py-2 rounded-lg text-center">Export to CSV</button>
                        <div>
                            <label for="csv_file" class="block text-sm font-medium text-gray-700 mb-1">Import CSV</label>
                            <input type="file" name="csv_file" id="csv_file" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 mb-2">
                            <button type="submit" name="import_csv" class="w-full bg-blue-600 text-white py-2 rounded-lg text-center">Import from CSV</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-bold">Recent Expenses</h2>
                <div class="flex space-x-4">
                    <select id="yearSelector" class="pl-3 pr-4 py-2 border rounded-lg">
                        <option value="">All Years</option>
                        <?php
                        // Generate year options dynamically, including future years
                        $currentYear = date('Y');
                        $endYear = $currentYear + 5; // Include up to 5 years in the future
                        for ($year = $currentYear; $year <= $endYear; $year++): ?>
                            <option value="<?= $year ?>"><?= $year ?></option>
                        <?php endfor; ?>
                    </select>
                    <select id="monthSelector" class="pl-3 pr-4 py-2 border rounded-lg">
                        <option value="">All Months</option>
                        <option value="january">January</option>
                        <option value="february">February</option>
                        <option value="march">March</option>
                        <option value="april">April</option>
                        <option value="may">May</option>
                        <option value="june">June</option>
                        <option value="july">July</option>
                        <option value="august">August</option>
                        <option value="september">September</option>
                        <option value="october">October</option>
                        <option value="november">November</option>
                        <option value="december">December</option>
                    </select>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200" id="expensesTable">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notes</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php
                        foreach ($expenses as $expense): 
                            $expenseDate = strtotime($expense['date']);
                            $monthStart = strtotime(date('Y-m-01', $expenseDate)); // First day of the month
                            $monthEnd = strtotime(date('Y-m-t', $expenseDate)); // Last day of the month

                            // Find the first Sunday before or on the first day of the month
                            $firstSunday = strtotime('last Sunday', $monthStart);
                            if (date('w', $monthStart) == 0) { // If the first day is Sunday, adjust
                                $firstSunday = $monthStart;
                            }

                            // Calculate the week number based on the calendar (Sunday to Saturday)
                            $weekNumber = 1 + floor(($expenseDate - $firstSunday) / (7 * 24 * 60 * 60));

                            // Ensure the week number is between 1 and 5
                            if ($weekNumber > 5) {
                                $weekNumber = 5;
                            }

                            $month = date('F', $expenseDate); // Get the month name
                            $year = date('Y', $expenseDate); // Get the year
                        ?>
                        <tr data-week="Week <?= $weekNumber ?> (<?= $month ?> <?= $year ?>)" data-month="<?= $month ?>" data-year="<?= $year ?>">
                            <td class="px-6 py-4 whitespace-nowrap"><?= date('M d, Y', $expenseDate) ?></td>
                            <td class="px-6 py-4 whitespace-nowrap"><?= $expense['category'] ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-red-600 font-medium">₱<?= number_format($expense['amount'], 2) ?></td>
                            <td class="px-6 py-4"><?= $expense['notes'] ? htmlspecialchars($expense['notes']) : '<span class="text-gray-400">No notes</span>' ?></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <a href="edit_expense.php?id=<?= $expense['id'] ?>" class="text-blue-600 hover:text-blue-800 mr-3"><i class="bi bi-pencil-square"></i></a>
                                <a href="delete_expense.php?id=<?= $expense['id'] ?>" class="text-red-600 hover:text-red-800"><i class="bi bi-trash"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        const expenseChart = new Chart(document.getElementById('expenseChart').getContext('2d'), {
            type: 'bar', // Change chart type to 'bar'
            data: {
                labels: <?php echo json_encode($chartData['labels']); ?>,
                datasets: [{
                    label: 'Expenses', // Add a label for the dataset
                    data: <?php echo json_encode($chartData['values']); ?>,
                    backgroundColor: [
                        '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#F7464A'
                    ],
                    borderColor: [
                        '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#F7464A'
                    ],
                    borderWidth: 1 // Add border width for better visualization
                }]
            },
            options: {
                responsive: true,
                scales: {
                    x: {
                        beginAtZero: true, // Ensure the x-axis starts at zero
                        title: {
                            display: true,
                            text: 'Categories' // Add a title for the x-axis
                        }
                    },
                    y: {
                        beginAtZero: true, // Ensure the y-axis starts at zero
                        title: {
                            display: true,
                            text: 'Amount (₱)' // Add a title for the y-axis
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true, // Show the legend
                        position: 'top' // Position the legend at the top
                    },
                    tooltip: {
                        enabled: true // Enable tooltips for better interactivity
                    }
                }
            }
        });

        const yearSelector = document.getElementById('yearSelector');
        const monthSelector = document.getElementById('monthSelector');
        const expensesTable = document.getElementById('expensesTable');
        const tableBody = expensesTable.querySelector('tbody');

        function filterExpenses() {
            const selectedYear = yearSelector.value.trim(); // Get the selected year
            const selectedMonth = monthSelector.value.toLowerCase().trim(); // Get the selected month
            const rows = Array.from(tableBody.querySelectorAll('tr')); // Convert NodeList to Array

            // Remove any existing week headers
            const existingHeaders = tableBody.querySelectorAll('.week-header');
            existingHeaders.forEach(header => header.remove());

            if (selectedYear === '' && selectedMonth === '') {
                // If no year or month is selected, show all rows
                rows.forEach(row => {
                    row.style.display = ''; // Show all rows
                });
                return;
            }

            const weekGroups = {}; // Object to group rows by week

            rows.forEach(row => {
                const rowYear = row.getAttribute('data-year'); // Get the year attribute
                const rowMonth = row.getAttribute('data-month').toLowerCase().trim(); // Get the month attribute
                const week = row.getAttribute('data-week'); // Get the week attribute

                // Check if the row matches the selected year and month
                const matchesYear = selectedYear === '' || rowYear === selectedYear;
                const matchesMonth = selectedMonth === '' || rowMonth === selectedMonth;

                if (matchesYear && matchesMonth) {
                    row.style.display = ''; // Show the row if it matches the filters

                    if (!weekGroups[week]) {
                        weekGroups[week] = []; // Initialize the week group if it doesn't exist
                    }
                    weekGroups[week].push(row); // Add the row to the corresponding week group
                } else {
                    row.style.display = 'none'; // Hide the row if it doesn't match
                }
            });

            // Sort the weeks in ascending order
            const sortedWeeks = Object.keys(weekGroups).sort((a, b) => {
                const weekNumberA = parseInt(a.match(/Week (\d+)/)[1], 10); // Extract the week number
                const weekNumberB = parseInt(b.match(/Week (\d+)/)[1], 10);
                return weekNumberA - weekNumberB;
            });

            // Append the rows with week headers in sorted order
            sortedWeeks.forEach(week => {
                const weekHeader = document.createElement('tr');
                weekHeader.classList.add('week-header');
                weekHeader.innerHTML = `
                    <td colspan="5" class="bg-gray-200 text-center font-bold py-2">${week}</td>
                `;
                tableBody.appendChild(weekHeader);

                weekGroups[week].forEach(row => {
                    tableBody.appendChild(row);
                });
            });
        }

        // Add event listeners for both selectors
        yearSelector.addEventListener('change', filterExpenses);
        monthSelector.addEventListener('change', filterExpenses);

        const menuToggle = document.getElementById('menuToggle');
        const mobileMenu = document.getElementById('mobileMenu');
        menuToggle.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });
    </script>
</body>
</html>
