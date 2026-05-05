<?php

include 'connectDB.php';
include 'auth.php';

authenticate();

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$response = [];

if ($conn) {

    $method = $_SERVER['REQUEST_METHOD'];

    switch ($method) {

        // ================= GET =================
        case 'GET':
            if (isset($_GET['id'])) {
                $id = intval($_GET['id']);
                $stmt = $conn->prepare("SELECT * FROM import_items WHERE id = ?");
                $stmt->bind_param("i", $id);
            } else {
                $stmt = $conn->prepare("SELECT * FROM import_items WHERE is_active = 1");
            }

            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $response[] = $row;
                }
            } else {
                $response = ["message" => "No data found"];
            }

            echo json_encode($response, JSON_PRETTY_PRINT);
            break;


        // ================= POST =================
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);

            $item_name = $data['item_name'] ?? "";
            $category = $data['category'] ?? "";
            $supplier = $data['supplier'] ?? "";
            $quantity = $data['quantity'] ?? 0;
            $import_price = $data['import_price'] ?? 0;
            $import_date = $data['import_date'] ?? "";
            $status = $data['status'] ?? "Pending";

            $stmt = $conn->prepare("INSERT INTO import_items 
                (item_name, category, supplier, quantity, import_price, import_date, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?)");

            $stmt->bind_param("sssisss", 
                $item_name, 
                $category, 
                $supplier, 
                $quantity, 
                $import_price, 
                $import_date, 
                $status
            );

            if ($stmt->execute()) {
                $response = ["message" => "Item added successfully"];
            } else {
                $response = ["message" => "Error inserting data: " . $conn->error];
            }

            echo json_encode($response);
            break;


        // ================= PUT =================
        case 'PUT':
            $data = json_decode(file_get_contents('php://input'), true);

            if (!isset($data['id'])) {
                echo json_encode(["message" => "ID not found"]);
                exit;
            }

            $id = intval($data['id']);
            $item_name = $data['item_name'] ?? "";
            $category = $data['category'] ?? "";
            $supplier = $data['supplier'] ?? "";
            $quantity = $data['quantity'] ?? 0;
            $import_price = $data['import_price'] ?? 0;
            $import_date = $data['import_date'] ?? "";
            $status = $data['status'] ?? "Pending";

            $stmt = $conn->prepare("UPDATE import_items SET 
                item_name=?, 
                category=?, 
                supplier=?, 
                quantity=?, 
                import_price=?, 
                import_date=?, 
                status=? 
                WHERE id=?");

            $stmt->bind_param("sssisssi", 
                $item_name, 
                $category, 
                $supplier, 
                $quantity, 
                $import_price, 
                $import_date, 
                $status, 
                $id
            );

            if ($stmt->execute()) {
                $response = ["message" => "Item updated successfully"];
            } else {
                $response = ["message" => "Error updating data: " . $conn->error];
            }

            echo json_encode($response);
            break;


        // ================= DELETE (SOFT DELETE) =================
        case 'DELETE':
            $data = json_decode(file_get_contents('php://input'), true);

            if (!isset($data['id'])) {
                echo json_encode(["message" => "ID not found"]);
                exit;
            }

            $id = intval($data['id']);

            $stmt = $conn->prepare("UPDATE import_items SET is_active = 0 WHERE id = ?");
            $stmt->bind_param("i", $id);

            if ($stmt->execute()) {
                $response = ["message" => "Item deleted successfully"];
            } else {
                $response = ["message" => "Error deleting data: " . $conn->error];
            }

            echo json_encode($response);
            break;


        // ================= DEFAULT =================
        default:
            echo json_encode(["message" => "Invalid request method"]);
    }
}

$conn->close();
?>