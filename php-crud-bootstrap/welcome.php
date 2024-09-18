<?php
include 'config.php';
session_start();

// Redirect to login page if not logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Handle the create operation
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['create'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    // Check if username or email already exists
    $checkSql = "SELECT * FROM users WHERE username = ? OR email = ?";
    $stmt = $conn->prepare($checkSql);
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if ($user['username'] == $username) {
            $_SESSION['message'] = 'Error: Username already exists.';
        } elseif ($user['email'] == $email) {
            $_SESSION['message'] = 'Error: Email already exists.';
        }
        $_SESSION['message_type'] = 'danger';
        $_SESSION['show_modal'] = true;
    } else {
        $sql = "INSERT INTO users (username, email, password, is_deleted) VALUES (?, ?, ?, 1)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $username, $email, $password);

        if ($stmt->execute()) {
            $_SESSION['message'] = 'User added successfully.';
            $_SESSION['message_type'] = 'success';
            $_SESSION['show_modal'] = true;
        } else {
            $_SESSION['message'] = 'Error: ' . $stmt->error;
            $_SESSION['message_type'] = 'danger';
            $_SESSION['show_modal'] = true;
        }
    }

    $stmt->close();
    header("Location: welcome.php");
    exit();
}

// Handle the delete operation
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $sql = "UPDATE users SET is_deleted = 0 WHERE ID=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $_SESSION['message'] = 'User marked as deleted successfully.';
        $_SESSION['message_type'] = 'success';
        $_SESSION['show_modal'] = true;
    } else {
        $_SESSION['message'] = 'Error: ' . $stmt->error;
        $_SESSION['message_type'] = 'danger';
        $_SESSION['show_modal'] = true;
    }

    $stmt->close();
    header("Location: welcome.php");
    exit();
}

// Fetch users where is_deleted is 1
$sql = "SELECT * FROM users WHERE is_deleted = 1";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Welcome</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            background-color: #ADD8E6;
            margin: 0;
            background-image: url('esa.jpg');
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center center;
            font-family: 'poppins', sans-serif;
        }
        .container {
            margin-top: 20px;
        }
        .card {
            margin: 0 auto;
            padding: 2em;
            width: 100%;
            max-width: 800px;
            background: #ffffff;
            text-align: center;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .card .table {
            margin-top: 20px;
        }
        .custom-input {
            font-size: 1.25rem;
            padding: 0.75rem;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
            text-align: left;
            width: 60%;
            max-width: 500px;
        }
        .form-group {
            width: 70%;
            text-align: left;
        }
        .image-container {
            text-align: center;
            margin-bottom: 20px;
        }
        .image-container img {
            width: 30%;
            max-width: 400px;
            height: auto;
            border-radius: 10px;
            display: block;
            bottom: 400px;
            margin-left: 450px;
            position: absolute;
            top: 170px;
        }
        .btn-custom {
            padding: 0.75rem 1.5rem;
            font-size: 1rem;
            border-radius: 0.5rem;
            background-color: #007bff;
            color: #ffffff;
            border: 1px solid #007bff;
            border: 2px solid #007bff;
        }
        .btn-custom:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }
        .button-container {
            left: 10;
            padding: 0.14rem;
            width: 21rem;
            font-size: 1rem;
            border-radius: 0.5rem;
            background-color: #007bff;
            color: #ffffff;
            border: 1px solid #007bff;
        }

    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <h2>Welcome, <?php echo $_SESSION['username']; ?>!</h2>
            <div class="d-flex flex-row-reverse">
                <a href="logout.php" class="btn btn-danger">Logout</a>
            </div>
            <div class="image-container">
                <img src="panis.jpg" alt="yo r gud">
            </div>

            <h3 class="form-group">Add User</h3>
            <form method="post" action="">
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" class="form-control custom-input" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" class="form-control custom-input" id="email" name="email" required>
                    <div id="email-warning" class="alert alert-danger" style="display: none;">
                        This email already exists.
                    </div>
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" class="form-control custom-input" id="password" name="password" required>
                </div>
                <div class="button-container">
                    <button type="submit" class="button-container" name="create" class="btn btn-primary">Add User</button>
                </div>
            </form>
            <h3 class="form-group" style="margin-top: 80px; margin-bottom: 0%; padding-bottom: 0px;">User List</h3>
            <table class="table table-bordered" style="margin-top: 0px; ">

                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['username']; ?></td>
                        <td><?php echo $row['email']; ?></td>
                        <td>
                            <a href="update.php?id=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                            <a href="?delete=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Structure -->
    <div class="modal fade" id="messageModal" tabindex="-1" role="dialog" aria-labelledby="messageModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="messageModalLabel">Notification</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <?php if (isset($_SESSION['message'])): ?>
                    <div class="alert alert-<?php echo $_SESSION['message_type']; ?>">
                        <?php echo $_SESSION['message']; unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
                    </div>
                    <?php endif; ?>
                    <img src="chineseman.jpg" alt="Image Description" class="img-fluid">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Include Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

    <script>
        $(document).ready(function(){
            <?php if (isset($_SESSION['show_modal']) && $_SESSION['show_modal']): ?>
                $('#messageModal').modal('show');
                <?php unset($_SESSION['show_modal']); ?>
            <?php endif; ?>
        });

        $('#email').on('blur', function() {
            var email = $(this).val();
            if (email) {
                $.ajax({
                    url: 'check_email.php',
                    type: 'POST',
                    data: { email: email },
                    success: function(response) {
                        if (response == 'exists') {
                            $('#email-warning').show();
                        } else {
                            $('#email-warning').hide();
                        }
                    }
                });
            }
        });
    </script>
</body>
</html>
<?php
$conn->close();
?>
