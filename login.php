<?php
    session_start();
    if(isset($_SESSION['username'])){
        header("Location: welcome.php");
    }
    
    include('connection.php');
    
    $login = false;
    if (isset($_POST['submit'])) {
        $username = $_POST['user'];
        $password = $_POST['pass'];
        
        $sql = "SELECT * FROM users WHERE username = '$username' OR email = '$username'";
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_array($result, MYSQLI_ASSOC);

        if($row) {
            if(password_verify($password, $row['password'])) {
                $_SESSION['username'] = $row['username'];
                $_SESSION['loggedin'] = true;
                header("Location: welcome.php");
            } else {
                echo '<script>alert("Invalid password!");</script>';
            }
        } else {
            echo '<script>alert("Invalid username or email!");</script>';
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .container {
            margin-top: 50px;
        }
        #form {
            max-width: 400px;
            margin: auto;
        }
        #heading {
            text-align: center;
            margin-bottom: 20px;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 id="heading">Login</h2>
        <form action="login.php" method="POST" required>
            <div class="mb-3">
                <label for="user" class="form-label">Username/Email</label>
                <input type="text" id="user" name="user" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="pass" class="form-label">Password</label>
                <input type="password" id="pass" name="pass" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary" name="submit">Login</button>
        </form>

        <div class="footer">
            <p>Don't have an account? <a href="signup.php">Sign Up</a></p>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"></script>
</body>
</html>
