<?php
    session_start();
    if (!isset($_SESSION['username'])) {
        header("Location: login.php");
        exit;
    }

    include('connection.php');

    $username = $_SESSION['username'];
    $sql_user = "SELECT id FROM users WHERE username = '$username'";
    $result_user = mysqli_query($conn, $sql_user);
    $user = mysqli_fetch_assoc($result_user);
    $user_id = $user['id'];

    $sql_expenses = "SELECT * FROM expenses WHERE user_id = $user_id";
    $result_expenses = mysqli_query($conn, $sql_expenses);
    $expenses = mysqli_fetch_all($result_expenses, MYSQLI_ASSOC);

    $total_expenses = 0;
    foreach ($expenses as $expense) {
        $total_expenses += $expense['amount'];
    }

    $categories = ['Food', 'Transport', 'Entertainment', 'Other'];
    $category_expenses = array_fill_keys($categories, 0);
    foreach ($expenses as $expense) {
        $category_expenses[$expense['category']] += $expense['amount'];
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $amount = $_POST['amount'];
        $category = $_POST['category'];
        $expense_date = $_POST['expense_date'];
        $description = $_POST['description'];

        $sql_add_expense = "INSERT INTO expenses (user_id, category, amount, expense_date, description) 
                            VALUES ($user_id, '$category', $amount, '$expense_date', '$description')";
        mysqli_query($conn, $sql_add_expense);
        header("Location: welcome.php");
    }

    if (isset($_GET['delete_id'])) {
        $delete_id = $_GET['delete_id'];
        $sql_delete = "DELETE FROM expenses WHERE id = $delete_id";
        mysqli_query($conn, $sql_delete);
        header("Location: welcome.php");
    }

    if (isset($_GET['edit_id'])) {
        $edit_id = $_GET['edit_id'];
        $sql_edit = "SELECT * FROM expenses WHERE id = $edit_id";
        $result_edit = mysqli_query($conn, $sql_edit);
        $edit_expense = mysqli_fetch_assoc($result_edit);
    }

    if (isset($_GET['logout'])) {
        session_destroy(); 
        header("Location: login.php"); 
        exit;
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expense Tracker - Welcome</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: 'Arial', sans-serif;
        }
        .container {
            margin-top: 50px;
        }
        #expense-table {
            margin-top: 30px;
        }
    </style>
</head>
<body>

    <div class="container">
        <h2>Expense Tracker Dashboard</h2>

        <a href="welcome.php?logout=true" class="btn btn-danger">Logout</a>

        <div class="row">
            <div class="col-md-6">
                <h4>Total Expenses for the Month</h4>
                <p id="total-expenses">$<?php echo number_format($total_expenses, 2); ?></p>
            </div>
            <div class="col-md-6">
                <h4>Expenses by Category</h4>
                <canvas id="expense-chart"></canvas>
            </div>
        </div>

        <div class="row mt-5">
            <div class="col-md-12">
                <h4>Add New Expense</h4>
                <form method="POST" action="welcome.php">
                    <div class="form-group">
                        <label for="amount">Amount</label>
                        <input type="number" id="amount" name="amount" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="category">Category</label>
                        <select id="category" name="category" class="form-control" required>
                            <option value="Food">Food</option>
                            <option value="Transport">Transport</option>
                            <option value="Entertainment">Entertainment</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="expense_date">Date</label>
                        <input type="date" id="expense_date" name="expense_date" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" class="form-control"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary mt-3">Add Expense</button>
                </form>
            </div>
        </div>


        <div class="row mt-5">
            <div class="col-md-12">
                <h4>Expense List</h4>
                <table class="table" id="expense-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Amount</th>
                            <th>Category</th>
                            <th>Date</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($expenses as $index => $expense): ?>
                            <tr>
                                <td><?php echo $index + 1; ?></td>
                                <td><?php echo $expense['amount']; ?></td>
                                <td><?php echo $expense['category']; ?></td>
                                <td><?php echo $expense['expense_date']; ?></td>
                                <td><?php echo $expense['description']; ?></td>
                                <td>
                                    <a href="welcome.php?edit_id=<?php echo $expense['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                    <a href="welcome.php?delete_id=<?php echo $expense['id']; ?>" class="btn btn-danger btn-sm">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        const categories = ['Food', 'Transport', 'Entertainment', 'Other'];
        const categoryExpenses = [<?php echo implode(',', $category_expenses); ?>];
        
        const ctx = document.getElementById("expense-chart").getContext("2d");
        new Chart(ctx, {
            type: 'pie',
            data: {
                labels: categories,
                datasets: [{
                    label: 'Expenses by Category',
                    data: categoryExpenses,
                    backgroundColor: ['#ff9999', '#66b3ff', '#99ff99', '#ffcc99'],
                    borderColor: ['#fff', '#fff', '#fff', '#fff'],
                    borderWidth: 1
                }]
            }
        });
    </script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"></script>
</body>
</html>
