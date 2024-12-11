<?php 
// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Include the database connection
require_once 'db.php';

// Get the logged-in user ID
$user_id = $_SESSION['user_id'];

// Fetch budget data for the user
try {
    $sql = "SELECT * FROM budget WHERE user_id = :user_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $budgets = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

// Fetch expense data for the user
try {
    $sql = "SELECT category, SUM(amount) as total_expense FROM expenses WHERE user_id = :user_id GROUP BY category";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $expenses = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // Organize expenses by category for easier comparison
    $expense_data = [];
    foreach ($expenses as $expense) {
        $expense_data[$expense['category']] = $expense['total_expense'];
    }
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expense Report</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css">
    <style>
        /* Print-only styling */
        @media print {
            body * {
                visibility: hidden;
            }
            .printable-table, .printable-table * {
                visibility: visible;
            }
            .printable-table {
                position: absolute;
                top: 0;
                left: 0;
            }
        }
        body{
            background: url(images/save1.jpg);
            background-position: center;
            background-size: cover;
            backdrop-filter:    blur(40px);
        }

        h1{
            color: red;
        }

    </style>
    <script>
        // JavaScript function to trigger print for the table only
        function printReport() {
            window.print(); // This opens the browser's print dialog
        }
    </script>
</head>
<body class="bg-light">
    
<ul class="nav justify-content-center">
  <li class="nav-item">
    <a class="nav-link active" aria-current="page" href="report.php">Report</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" href="expenses.php">Expenses</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" href="budget.php">Budget</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" href="dashboard.php">Dashboard</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" href="logout.php">Logout</a>
  </li>
</ul>
    <div class="container mt-5">
        <h2 class="text-center">Expense Report</h2>

        <?php if (!empty($budgets)): ?>
            <!-- Div that contains the table for printing -->
            <div class="table-responsive printable-table">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Category</th>
                            <th>Budget Amount</th>
                            <th>Total Expenses</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($budgets as $budget): 
                            $category = $budget['category'];
                            $budget_amount = $budget['amount'];
                            $start_date = $budget['start_date'];
                            $end_date = $budget['end_date'];
                            $status = "Low"; // Default to low

                            // If expenses exist for this category, compare them
                            $total_expense = $expense_data[$category] ?? 0;

                            // If expenses exceed budget, set status to "High"
                            if ($total_expense > $budget_amount) {
                                $status = "High";
                            }
                        ?>
                            <tr>
                                <td><?php echo htmlspecialchars($category); ?></td>
                                <td><?php echo htmlspecialchars($budget_amount); ?></td>
                                <td><?php echo htmlspecialchars($total_expense); ?></td>
                                <td><?php echo htmlspecialchars($status); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p>No budgets found for this user.</p>
        <?php endif; ?>
    </div>
    <div class="text-center mb-3">
            <button class="btn btn-primary" onclick="printReport()">Print Report</button>
        </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
