<?php
session_start();
include('connection.php');

if (!isset($_SESSION['loggedin'])) {
    header("Location: login.php");
    exit();
}

$user = $_SESSION['username'];
$user_sql = "SELECT id FROM users WHERE username = '$user'";
$user_result = mysqli_query($conn, $user_sql);
$user_row = mysqli_fetch_assoc($user_result);
$user_id = $user_row['id'];

if (isset($_POST['submit'])) {
    $category = $_POST['category'];
    $amount = $_POST['amount'];
    $expense_date = $_POST['expense_date'];
    $description = $_POST['description'];

    $sql = "INSERT INTO expenses (user_id, category, amount, expense_date, description) VALUES ('$user_id', '$category', '$amount', '$expense_date', '$description')";
    
    if (mysqli_query($conn, $sql)) {
        echo '<script>alert("Expense added successfully!")</script>';
    } else {
        echo '<script>alert("Error adding expense!")</script>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Expense</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Add New Expense</h1>
        <form action="add_expense.php" method="POST">
            <label>Category</label>
            <input type="text" name="category" class="form-control" required><br>
            <label>Amount</label>
            <input type="number" name="amount" class="form-control" required><br>
            <label>Expense Date</label>
            <input type="date" name="expense_date" class="form-control" required><br>
            <label>Description</label>
            <textarea name="description" class="form-control"></textarea><br>
            <input type="submit" name="submit" value="Add Expense" class="btn btn-primary">
        </form>
    </div>
</body>
</html>
