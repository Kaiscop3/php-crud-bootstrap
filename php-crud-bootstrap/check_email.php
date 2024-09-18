<?php
include 'config.php'; // Ensure this file has the $conn variable for the database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $id = $_POST['id'];

    // Check if email already exists for a different user
    $sql = "SELECT * FROM users WHERE email = ? AND ID != ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('si', $email, $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode(['status' => 'exists']);
    } else {
        echo json_encode(['status' => 'not_exists']);
    }

    $stmt->close();
    $conn->close();
}
?>
