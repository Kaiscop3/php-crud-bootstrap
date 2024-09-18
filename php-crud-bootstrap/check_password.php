<?php
include 'config.php'; // Ensure this file has the $conn variable for the database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $current_password = $_POST['current_password'];
    $id = $_POST['id'];

    // Check current password
    $sql = "SELECT password FROM users WHERE ID=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    if ($user && password_verify($current_password, $user['password'])) {
        echo json_encode(['status' => 'correct']);
    } else {
        echo json_encode(['status' => 'incorrect']);
    }

    $conn->close();
}
