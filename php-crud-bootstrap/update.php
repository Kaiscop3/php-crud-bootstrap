<?php
include 'config.php'; // Ensure this file has the $conn variable for the database connection
session_start();

// Redirect to login page if not logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Check if the form is submitted for user update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $current_password = $_POST['current_password'];
    $new_password = !empty($_POST['new_password']) ? password_hash($_POST['new_password'], PASSWORD_BCRYPT) : null;

    // Check current password
    $checkSql = "SELECT password FROM users WHERE ID=?";
    $stmt = $conn->prepare($checkSql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    if ($user && password_verify($current_password, $user['password'])) {
        // Update user details
        if ($new_password) {
            $sql = "UPDATE users SET username=?, email=?, password=? WHERE ID=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssi", $username, $email, $new_password, $id);
        } else {
            $sql = "UPDATE users SET username=?, email=? WHERE ID=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssi", $username, $email, $id);
        }

        if ($stmt->execute()) {
            $_SESSION['message'] = "User updated successfully.";
        } else {
            $_SESSION['message'] = "Error: " . $stmt->error;
        }

        $stmt->close();
    } else {
        $_SESSION['message'] = "Current password is incorrect.";
    }

    header("Location: welcome.php");
    exit();
}

// Get user details for the given ID
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM users WHERE ID=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit User</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <style>
        body {
            background-color: #ADD8E6;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
            font-family: 'poppins', sans-serif;
            background-image: url('esa.jpg');
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center center;
        }
        .update-container {
            width: 100%;
            max-width: 400px;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 10px;
            text-align: center;
            position: relative;
        }
        .form-group {
            text-align: left;
        }
    </style>
</head>
<body>
    <div class="container update-container">
        <div class="card">
            <div class="card-body">
                <h3>Edit User</h3>
                <?php if (isset($user)): ?>
                    <?php if (isset($_GET['error']) && $_GET['error'] == 'email_exists'): ?>
                        <div class="alert alert-danger">This email already exists. Please choose a different email.</div>
                    <?php endif; ?>
                    <?php if (isset($_GET['error']) && $_GET['error'] == 'incorrect_password'): ?>
                        <div class="alert alert-danger">The current password is incorrect. Please try again.</div>
                    <?php endif; ?>
                    <form id="editUserForm" method="post" action="">
                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($user['ID']); ?>">
                        <div class="form-group">
                            <label for="username">Username:</label>
                            <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email:</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="current_password">Current Password:</label>
                            <input type="password" class="form-control" id="current_password" name="current_password" required>
                        </div>
                        <div class="form-group">
                            <label for="new_password">New Password (leave blank if not changing):</label>
                            <input type="password" class="form-control" id="new_password" name="new_password">
                        </div>
                        <button type="submit" id="updateButton" class="btn btn-primary">Update User</button>
                        <a href="welcome.php" class="btn btn-secondary">Cancel</a>
                    </form>
                <?php else: ?>
                    <div class="alert alert-danger">User not found.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Include Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#email').on('blur', function() {
                var email = $(this).val();
                var id = $('input[name="id"]').val();
                
                if (email) {
                    $.ajax({
                        url: 'check_email.php',
                        type: 'POST',
                        data: { email: email, id: id },
                        success: function(response) {
                            console.log(response); // Debugging: Log response to console
                            var data = JSON.parse(response);
                            if (data.status == 'exists') {
                                window.location.href = 'update.php?id=' + id + '&error=email_exists';
                            }
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            console.error('AJAX error: ' + textStatus + ', ' + errorThrown);
                        }
                    });
                }
            });

            $('#current_password').on('blur', function() {
                var current_password = $(this).val();
                var id = $('input[name="id"]').val();
                
                if (current_password) {
                    $.ajax({
                        url: 'check_password.php',
                        type: 'POST',
                        data: { current_password: current_password, id: id },
                        success: function(response) {
                            console.log(response); // Debugging: Log response to console
                            var data = JSON.parse(response);
                            if (data.status == 'incorrect') {
                                window.location.href = 'update.php?id=' + id + '&error=incorrect_password';
                            }
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            console.error('AJAX error: ' + textStatus + ', ' + errorThrown);
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>
