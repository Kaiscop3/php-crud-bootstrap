<?php
include 'config.php';
session_start();

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $conn->real_escape_string($_POST['username']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Check if username or email already exists
    $checkUserSql = "SELECT * FROM users WHERE username='$username' OR email='$email'";
    $checkUserResult = $conn->query($checkUserSql);

    if ($checkUserResult->num_rows > 0) {
        $message = "Username or email already exists.";
    } else {
        $sql = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$password')";
        
        if ($conn->query($sql) === TRUE) {
            $message = "Registration Successful!";
        } else {
            $message = "Error: " . $conn->error;
        }
    }
    $conn->close();

    // Redirect to the same page with a query parameter for the message
    header("Location: register.php?message=" . urlencode($message));
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            background-color: #ADD8E6;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
            background-image: url('esa.jpg');
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center center;
            font-family: 'poppins', sans-serif;
        }
        .welcome-container {
            width: 30%;
            max-width: 600px;
            text-align: center;
            border-radius: 10px;
            position: relative;
        }
        .custom-input {
            font-size: 1rem; /* Adjust font size */
            width: 60%;
            padding: 0.75rem; /* Adjust padding */
            border-radius: 0.5rem; /* Adjust border radius */
            border: 1px solid #007bff; /* Adjust border color and width */
            background-color: #f8f9fa;
            margin: 0 auto;
            display: block;
        }
        .welcome-container::after, .welcome-container::before {
            content: '';
            position: absolute;
            height: 105%;
            width: 103%;
            background-image: conic-gradient(from var(--angle),transparent 70%,red);
            top: 50%;
            left: 50%;
            translate: -50% -50%;
            z-index: -1;
            padding: 3px;
            border-radius: 10px;
        }
        @property --angle {
            syntax: "<angle>";
            initial-value: 360deg;
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
        .form-group {
            text-align: justify;
        }
        .input-group i {
            margin-left: 10px; /* Space between the input and icon */
            color: #007bff; /* Icon color */
            font-size: 2.1rem; /* Adjust icon size */
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <!-- Modal -->
        <div class="modal fade" id="messageModal" tabindex="-1" aria-labelledby="messageModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="messageModalLabel">Message</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <?php echo isset($_GET['message']) ? htmlspecialchars($_GET['message']) : ''; ?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="container welcome-container">
            <h1>REGISTER</h1>
            <form method="post" action="">
                <div class="form-group">
                    <label for="username">Username:</label>
                    <div class="input-group">
                        <input type="text" class="form-control custom-input" id="username" name="username" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <div class="input-group">
                        <input type="email" class="form-control custom-input" id="email" name="email" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <div class="input-group">
                        <input type="password" class="form-control custom-input" id="password" name="password" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Register</button>
                <a href="login.php" class="btn btn-secondary">Login</a>
            </form>
        </div>
    </div>

    <!-- Include Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

    <!-- Show the modal if there's a message -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            <?php if (isset($_GET['message'])): ?>
                $('#messageModal').modal('show');
            <?php endif; ?>
        });
    </script>
</body>
</html>
