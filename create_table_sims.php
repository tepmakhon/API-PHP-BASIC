<?php
include 'connect-database.php';

$createTableSQL = "CREATE TABLE IF NOT EXISTS import_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    item_name VARCHAR(150) NOT NULL,
    category VARCHAR(100) NOT NULL,
    supplier VARCHAR(100) NOT NULL,
    quantity INT NOT NULL,
    import_price DECIMAL(10,2) NOT NULL,
    import_date DATE NOT NULL,
    status ENUM('Pending','Completed') DEFAULT 'Pending',
    is_active BOOLEAN DEFAULT 1,
    create_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    update_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

if ($conn->query($createTableSQL) === TRUE) {
    echo "Table created successfully!";
} else {
    echo "Error: " . $conn->error;
}

$conn->close();
?>