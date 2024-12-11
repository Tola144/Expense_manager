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

// Fetch user information
$user_id = $_SESSION['user_id'];
try {
    $sql = "SELECT username FROM users WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $user_id);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css">

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
            color: #173660;
        }
    </style>
</head>

<body>
    

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
        <div class="row">
            <div class="col-md-12 text-center">
            <p>What would you like Add on your budget and expense</p>
                <h1>Welcome, <?php echo htmlspecialchars($user['username']); ?>!</h1>
                
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title">Monitor expenses</h5>
                        <a href="expenses.php" class="btn btn-primary">Visit</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title">Go budget</h5>
                        <a href="budget.php" class="btn btn-primary">visit</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title">Generate Reports</h5>
                        <a href="report.php" class="btn btn-primary">Go</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
