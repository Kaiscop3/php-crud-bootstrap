<?php
include 'config.php';

$sql = "ALTER TABLE users ADD is_deleted TINYINT(1) DEFAULT 1";

if ($conn->query($sql) === TRUE) {
    echo "Column added successfully";
} else {
    echo "Error: " . $conn->error;
}

$conn->close();
?>
