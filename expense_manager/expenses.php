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

// Handle form submission to add a new expense
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $description = $_POST['description'] ?? '';
    $amount = $_POST['amount'] ?? 0;
    $category = $_POST['category'] ?? '';
    $expense_date = $_POST['date'] ?? date('Y-m-d');

    try {
        $sql = "INSERT INTO expenses (user_id, description, amount, category, expense_date) 
                VALUES (:user_id, :description, :amount, :category, :expense_date)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([ 
            ':user_id' => $user_id,
            ':description' => $description,
            ':amount' => $amount,
            ':category' => $category,
            ':expense_date' => $expense_date
        ]);
        $message = "Expense added successfully.";
    } catch (PDOException $e) {
        $message = "Error: " . $e->getMessage();
    }
}

// Handle the expense deletion
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];

    try {
        $sql = "DELETE FROM expenses WHERE id = :delete_id AND user_id = :user_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([ 
            ':delete_id' => $delete_id,
            ':user_id' => $user_id
        ]);
        $message = "Expense deleted successfully.";
    } catch (PDOException $e) {
        $message = "Error: " . $e->getMessage();
    }
}

// Fetch all expenses for the logged-in user
try {
    $sql = "SELECT * FROM expenses WHERE user_id = :user_id ORDER BY expense_date DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $expenses = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Expenses</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css">
    <style>
        body{
            background: url(images/save1.jpg);
            background-position: center;
            background-size: cover;
            backdrop-filter:    blur(20px);
        }

        h1{
            color: red;
        }
    </style>
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
        <h2 class="text-center">Track Your Expenses</h2>
        <?php if (!empty($message)): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        <div class="row">
            <div class="col-md-6">
                <h4>Add New Expense</h4>
                <form method="post">
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <input type="text" class="form-control" id="description" name="description" required>
                    </div>
                    <div class="mb-3">
                        <label for="amount" class="form-label">Amount</label>
                        <input type="number" class="form-control" id="amount" name="amount" step="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label for="category" class="form-label">Category</label>
                        <select class="form-select" id="category" name="category" required>
                            <option value="tuition_fees">Tuition Fees</option>
                            <option value="food_meals">Meals</option>
                            <option value="accommodation">Accommodation</option>
                            <option value="personal_expenses">Personal Expenses</option>
                            <option value="transport">Transport</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="date" class="form-label">Date</label>
                        <input type="date" class="form-control" id="date" name="date" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Add Expense</button>
                </form>
            </div>
            <div class="col-md-6">
                
    </div>
    <h4>Your Expenses</h4>
                <?php if (!empty($expenses)): ?>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Description</th>
                                <th>Amount</th>
                                <th>Category</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($expenses as $expense): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($expense['description']); ?></td>
                                    <td><?php echo htmlspecialchars($expense['amount']); ?></td>
                                    <td><?php echo htmlspecialchars($expense['category']); ?></td>
                                    <td><?php echo htmlspecialchars($expense['expense_date']); ?></td>
                                    <td>
                                        <a href="?delete_id=<?php echo $expense['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this expense?')">Delete</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No expenses recorded yet.</p>
                <?php endif; ?>
            </div>
        </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
