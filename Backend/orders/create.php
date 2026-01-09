<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, DELETE, PUT");
header("Access-Control-Allow-Headers: Content-Type");

$conn = mysqli_connect("localhost", "root", "", "ecommerce");

if (!$conn) {
    echo json_encode(["success" => false, "message" => "DB connection failed"]);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

/* ---------- GET (Read) ---------- */
if ($method === "GET") {
    $sql = "SELECT * FROM `orders`";
    $result = mysqli_query($conn, $sql);

    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }

    echo json_encode($data);
}
    

/* ---------- POST  ---------- */

if ($method === "POST") {

    $data = json_decode(file_get_contents('php://input'), true);

    $customer_name  = mysqli_real_escape_string($conn, $data['customer_name'] ?? '');
    $customer_address  = mysqli_real_escape_string($conn, $data['customer_address'] ?? '');
    $customer_phone  = mysqli_real_escape_string($conn, $data['customer_phone'] ?? '');
    $total_price  = mysqli_real_escape_string($conn, $data['total_price'] ?? '');

    $thumbnail  = mysqli_real_escape_string($conn, $data['thumbnail'] ?? '');

    $product_name  = mysqli_real_escape_string($conn, $data['product_name'] ?? '');


        $sql = "INSERT INTO `orders` ( `customer_name`, `customer_address`, `customer_phone`, `total_price`, `order_status`, `thumbnail`, `product_name`) VALUES ('$customer_name', '$customer_address', '$customer_phone', '$total_price', 'pending','$thumbnail', '$product_name');";


        if (mysqli_query($conn, $sql)) {
        echo json_encode(["success" => true, "message" => "Product added successfully"]);
        } else {
            echo json_encode(["success"=> false, "message"=> mysqli_error($conn)]);
        }
        
}


/* ---------- DELETE ---------- */

if( $method == 'DELETE'){
    $data = json_decode(file_get_contents('php://input'), true);

    $id = $data['id'] ?? null;

    if ($id) {
        $sql = "DELETE FROM `orders` WHERE `orders`.`order_id` = $id";
        if (mysqli_query($conn, $sql)) {
            echo json_encode(["success" => true, "message" => "Account deleted successfully"]);
        } else {
            echo json_encode(["success" => false, "message" => "Failed to delete account"]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "ID is required to delete account"]);
    }

}

/* ---------- PUT (UPDATE ORDER STATUS) ---------- */
if ($method === "PUT") {
    $data = json_decode(file_get_contents("php://input"), true);

    $id = $data['id'] ?? null;
    $status = $data['status'] ?? null;

    if ($id && $status) {
        $sql = "UPDATE `orders` SET `order_status`='$status' WHERE `order_id`=$id";
        if (mysqli_query($conn, $sql)) {
            echo json_encode(["success" => true, "message" => "Order updated"]);
        } else {
            echo json_encode(["success" => false, "message" => "Update failed"]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "ID & status required"]);
    }
}
