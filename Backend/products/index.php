<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, DELETE");
header("Access-Control-Allow-Headers: Content-Type");

$conn = mysqli_connect("localhost", "root", "", "ecommerce");

if (!$conn) {
    echo json_encode(["success" => false, "message" => "DB connection failed"]);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

/* ---------- GET (Read) ---------- */
if ($method === "GET") {
    $sql = "SELECT * FROM `products`";
    $result = mysqli_query($conn, $sql);

    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }

    echo json_encode($data);
}

/* ---------- POST  ---------- */
if ($method === "POST") {
    $data = json_decode(file_get_contents("php://input"), true);

    // Null coalescing operator ব্যবহার করে warnings এড়ানো
    $title  = mysqli_real_escape_string($conn, $data['title'] ?? '');
    $description = mysqli_real_escape_string($conn, $data['description'] ?? '');
    $category = mysqli_real_escape_string($conn, $data['category'] ?? '');
    $brand = mysqli_real_escape_string($conn, $data['brand'] ?? '');
    $thumbnail = mysqli_real_escape_string($conn, $data['thumbnail'] ?? '');
    $warranty_information = mysqli_real_escape_string($conn, $data['warranty'] ?? '');
    $shipping_information = mysqli_real_escape_string($conn, $data['shipping'] ?? '');
    $availability_status = mysqli_real_escape_string($conn, $data['availability'] ?? '');
    $return_policy = mysqli_real_escape_string($conn, $data['returnPolicy'] ?? '');

    // Number fields typecast
    $price = floatval($data['price'] ?? 0);
    $discount = floatval($data['discount'] ?? 0);
    $rating = floatval($data['rating'] ?? 0);
    $stock = intval($data['stock'] ?? 0);

    // Simple insert query
    $sql = "INSERT INTO `products` 
    (`title`, `description`, `category`, `price`, `discount`, `rating`, `stock`, `brand`, `thumbnail`, `warranty_information`, `shipping_information`, `availability_status`, `return_policy`) 
    VALUES ('$title', '$description', '$category', $price, $discount, $rating, $stock, '$brand', '$thumbnail', '$warranty_information', '$shipping_information', '$availability_status', '$return_policy')";

    if (mysqli_query($conn, $sql)) {
        echo json_encode(["success" => true, "message" => "Product added successfully"]);
    } else {
        echo json_encode(["success"=> false, "message"=> mysqli_error($conn)]);
    }
}

/* ---------- DELETE ---------- */
if ($method === "DELETE") {
  
    $data = json_decode(file_get_contents("php://input"), true);

    $id = $data['id'] ?? null;

    if ($id) {
        $sql = "DELETE FROM `products` WHERE `products`.`id` = $id";
        if (mysqli_query($conn, $sql)) {
            echo json_encode(["success" => true, "message" => "Account deleted successfully"]);
        } else {
            echo json_encode(["success" => false, "message" => "Failed to delete account"]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "ID is required to delete account"]);
    }
}