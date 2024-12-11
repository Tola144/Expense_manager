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



// Handle form submission to create a new budget

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $budget_amount = $_POST['budget_amount'] ?? 0;
   $category = $_POST['category'] ?? ''; // New category field

    $start_date = $_POST['start_date'] ?? date('Y-m-d'); // Default to current date
    $end_date = $_POST['end_date'] ?? date('Y-m-d'); // Default to current date



    try {

// Insert the budget into the database

  $sql = "INSERT INTO budget (user_id, category, amount, start_date, end_date) 
  VALUES (:user_id, :category, :budget_amount, :start_date, :end_date)";

  $stmt = $pdo->prepare($sql);

  $stmt->execute([
  ':user_id' => $user_id,
  ':category' => $category,
  ':budget_amount' => $budget_amount,
  ':start_date' => $start_date,
  ':end_date' => $end_date
 ]);

$message = "Budget created successfully.";
     
} catch (PDOException $e) {

 $message = "Error: " . $e->getMessage();
}

}



// Fetch existing budgets for the logged-in user

try {

$sql = "SELECT * FROM budget WHERE user_id = :user_id ORDER BY start_date DESC";
 $stmt = $pdo->prepare($sql);
 $stmt->bindParam(':user_id', $user_id);
 $stmt->execute();
 $budgets = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {

die("Error: " . $e->getMessage());

}

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Create Budget</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="styles.css">

<style>
        .card-body{
            background-color: #80A4AE;
        }

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
        <h2 class="text-center">Create a Budget</h2>
        <?php if (!empty($message)): ?>

 <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>

<?php endif; ?>

<div class="row">
  <div class="col-md-6">
           <h4>Set Your Budget</h4>

             <form method="post">

                  <div class="mb-3">

                       <label for="category" class="form-label">Category</label>

                         <select class="form-control" id="category" name="category" required>

                             <option value="">Select a category</option>

                            <option value="tuition_fees">Tuition Fees</option>

                            <option value="food_meals">Food & Meals</option>

                            <option value="accommodation">Accommodation</option>

                            <option value="personal_expenses">Personal Expenses</option>

                            <option value="transport">Transport</option>

                         </select>

                 </div>

                     <div class="mb-3">

                         <label for="budget_amount" class="form-label">Budget Amount</label>

                       <input type="number" class="form-control" id="budget_amount" name="budget_amount" step="0.01" required>

                    </div>

                    <div class="mb-3">

                        <label for="start_date" class="form-label">Start Date</label>

                        <input type="date" class="form-control" id="start_date" name="start_date" required>

                      </div>

                      <div class="mb-3">

                        <label for="end_date" class="form-label">End Date</label>

                      <input type="date" class="form-control" id="end_date" name="end_date" required>

                         </div>

                     <button type="submit" class="btn btn-primary">Create Budget</button>

                 </form>

            </div>

               <div class="col-md-6">

                 <h4>Your Budgets</h4>

                 <?php if (!empty($budgets)): ?>

                     <table class="table table-striped">

                         <thead>

                            <tr>

                                <th>Start Date</th>

                                <th>End Date</th>

                                <th>Category</th>

                                <th>Budget Amount</th>

                                <th>Created At</th>

                 </tr>

                    </thead>

                        <tbody>

                            <?php foreach ($budgets as $budget): ?>

                                <tr>

                                     <td><?php echo htmlspecialchars($budget['start_date']); ?></td>

                                    <td><?php echo htmlspecialchars($budget['end_date']); ?></td>

                                     <td><?php echo htmlspecialchars($budget['category']); ?></td>

                                     <td><?php echo htmlspecialchars($budget['amount']); ?></td>

                                     <td><?php echo htmlspecialchars($budget['created_at']); ?></td>

                                 </tr>

                             <?php endforeach; ?>

                        </tbody>

                    </table>

                <?php else: ?>

                    <p>No budgets set yet.</p>

                <?php endif; ?>

           </div>

        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html> 