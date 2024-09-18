<?php
include 'config.php';
session_start();

$message = ""; // Initialize a variable to store the message

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE username='$username' AND is_deleted=1";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])){
            $_SESSION['username'] = $username;
            header("Location: welcome.php");
            exit();
        } else {
            $message = "Invalid password.";
        }
    } else {
        $message = "Invalid Username/Password or the account does not exist";
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="login.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        body {
            background-color: #ADD8E6;
            height: 100vh; /* Full viewport height */
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            box-sizing: border-box;
            font-family: 'poppins', sans-serif;
            background-image: url('esa.jpg');
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center center;
        }
        .welcome-container {
            width: 100%;
            max-width: 400px; /* Set a max-width for the card */
            padding: 20px; /* Add padding inside the card */
            background-color: #ffffff; /* White background for the card */
            border-radius: 10px; /* Rounded corners */
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); /* Add shadow effect */
            text-align: center; /* Center text inside the card */
            position: relative;
        }
        .custom-input {
            font-size: 1rem; /* Adjust font size */
            width: 90%; /* Full width of the container */
            padding: 0.75rem; /* Adjust padding */
            border-radius: 0.5rem; /* Adjust border radius */
            border: 1px solid #007bff; /* Adjust border color and width */
            background-color: #f8f9fa;
        }
        .message {
            margin-bottom: 20px;
            color: red;
        }
        .welcome-container::after, .welcome-container::before {
            content: '';
            position: absolute;
            height: 105%;
            width: 105%;
            background-image: conic-gradient(from var(--angle), transparent 70%, red);
            top: 50%;
            left: 50%;
            translate: -50% -50%;
            z-index: -1;
            padding: 3px;
            border-radius: 10px;
            animation: 3s spin linear infinite;
        }
        @property --angle {
            syntax: "<angle>";
            initial-value: 0deg;
            inherits: false;
        }
        .welcome-container::before {
            filter: blur(1.5rem);
            opacity: 0.5;
        }
        @keyframes spin {
            from {
                --angle: 0deg;
            }
            to {
                --angle: 360deg;
            }
        }
        .input-group i {
            margin-left: 10px; /* Space between the input and icon */
            color: #007bff; /* Icon color */
            font-size: 2.1rem; /* Adjust icon size */
        }
        .form-group {
            text-align: left;
        }
        .btn-link {
            display: inline-block;
            font-size: 1rem;
            font-weight: 400;
            color: #007bff;
            text-align: center;
            background-color: transparent;
            border: 1px solid transparent;
            border-radius: 0.25rem;
            padding: 0.5rem 1rem;
            text-decoration: none;
        }
        .btn-link:hover {
            color: #0056b3;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container welcome-container">
        <h1>LOGIN</h1>
        <div class="message"><?php echo htmlspecialchars($message); ?></div>
        <form method="post">
            <div class="form-group">
                <label for="username">Username:</label>
                <div class="input-group">
                    <input type="text" class="form-control custom-input" id="username" name="username" placeholder="Enter your username" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" required>
                    <i class='bx bxs-user'></i>
                </div>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <div class="input-group">
                    <input type="password" class="form-control custom-input" id="password" name="password" placeholder="Enter your password" value="<?php echo isset($_POST['password']) ? htmlspecialchars($_POST['password']) : ''; ?>" required>
                    <i class='bx bxs-lock'></i>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Login</button>
            <a href="register.php" class="btn btn-link">Create an account</a>
        </form>
    </div>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>
</html>
