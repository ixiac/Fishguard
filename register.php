<?php
require_once 'assets/db.php';

// SECTION: Handle registration form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["username"], $_POST["password"], $_POST["name"], $_POST["address"], $_POST["contact_no"])) {
    $username = $conn->real_escape_string($_POST["username"]);
    $password = password_hash($conn->real_escape_string($_POST["password"]), PASSWORD_BCRYPT);
    $name = $conn->real_escape_string($_POST["name"]);
    $address = $conn->real_escape_string($_POST["address"]);
    $contact_no = $conn->real_escape_string($_POST["contact_no"]);
    $role = 2;

    $checkUserQuery = "SELECT * FROM users WHERE username = '$username'";
    $result = $conn->query($checkUserQuery);

    if ($result->num_rows > 0) {
        $registerError = "Username already exists.";
    } else {
        $sql = "INSERT INTO users (username, password, name, address, contact_no, role) VALUES ('$username', '$password', '$name', '$address', '$contact_no', $role)";
        if ($conn->query($sql) === TRUE) {
            $registerSuccess = "Registration successful! You can now log in.";
        } else {
            $registerError = "Error: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- SECTION: Meta and Styles -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - FishGuard</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body, html {
            height: 100%;
            margin: 0;
            background-color: #f8f9fa;
        }
        .register-container {
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 50px;
        }
        .slogan {
            max-width: 50%;
            text-align: center;
        }
        .slogan img {
            width: 150px;
            height: 150px;
            margin-bottom: 20px;
        }
        .slogan h1 {
            font-size: 3rem;
            color: #007bff;
            font-weight: bold;
        }
        .slogan p {
            font-size: 1.2rem;
            color: #6c757d;
        }
        .register-form {
            width: 40%;
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .btn-primary {
            background-color: #007bff;
            border: none;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <!-- SECTION: Slogan -->
        <div class="slogan">
            <img src="assets/img/logo.png" alt="FishGuard Logo">
            <h1>Join Us as a Fisherman</h1>
            <p>Become part of the FishGuard community and simplify your fishing compliance. Track your catches, stay updated on regulations, and fish sustainably with ease.</p>
        </div>

        <!-- SECTION: Register Form -->
        <div class="register-form">
            <?php if (isset($registerError)): ?>
                <div class="alert alert-danger"><?php echo $registerError; ?></div>
            <?php endif; ?>
            <?php if (isset($registerSuccess)): ?>
                <div class="alert alert-success"><?php echo $registerSuccess; ?></div>
            <?php endif; ?>
            <h2 class="text-center mb-4">Register</h2>
            <form action="" method="POST">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" name="username" placeholder="Enter your username" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
                </div>
                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" class="form-control" id="name" name="name" placeholder="Enter your full name" required>
                </div>
                <div class="mb-3">
                    <label for="address" class="form-label">Address</label>
                    <input type="text" class="form-control" id="address" name="address" placeholder="Enter your address" required>
                </div>
                <div class="mb-3">
                    <label for="contact_no" class="form-label">Contact Number</label>
                    <input type="text" class="form-control" id="contact_no" name="contact_no" placeholder="Enter your contact number" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Register</button>
            </form>
            <div class="text-center mt-3">
                <p>Already have an account? <a href="index.php" class="text-primary">Login here</a>.</p>
            </div>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
