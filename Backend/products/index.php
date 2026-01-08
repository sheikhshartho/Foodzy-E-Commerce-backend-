<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, DELETE , PUT ");
header("Access-Control-Allow-Headers: Content-Type");

$conn = mysqli_connect("localhost", "root", "", "ecommerce");

if (!$conn) {
    echo json_encode(["success" => false, "message" => "DB connection failed"]);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

/* ---------- GET (Read) ---------- */
if ($method === "GET") {

    // Check if "id" is passed in query string
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']); // ensure numeric

        $sql = "SELECT * FROM `products` WHERE id = $id";
        $result = mysqli_query($conn, $sql);
        $product = mysqli_fetch_assoc($result);

        if ($product) {
            echo json_encode($product);
        } else {
            http_response_code(404);
            echo json_encode(["success" => false, "message" => "Product not found"]);
        }
    } else {
        // No id → return all products
        $sql = "SELECT * FROM `products`";
        $result = mysqli_query($conn, $sql);

        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }

        echo json_encode($data);
    }
}


/* ---------- POST  ---------- */
if ($method === "POST") {
    $data = json_decode(file_get_contents("php://input"), true);

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
    (`title`, `description`, `category`, `price`, `discount`, `rating`, `stock`, `brand`, `thumbnail`, `warranty_information`, `shipping_information`, `availability_status`, `return_policy` ,`postType`) 
    VALUES ('$title', '$description', '$category', $price, $discount, $rating, $stock, '$brand', '$thumbnail', '$warranty_information', '$shipping_information', '$availability_status', '$return_policy' , 'Publish')";

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

/* ---------- PUT (Update) ---------- */
if($method === 'PUT'){
    $data = json_decode(file_get_contents('php://input'), true);

    $id = $data['id'] ?? null;

    if(!$id){
        echo json_encode(['success'=> false, 'message'=> 'ID is required']);
        exit;
    }

    $fields = [];

    if (!empty($data['postType'])){
        $postType = mysqli_real_escape_string($conn, $data['postType']);
        $fields[] = "`postType` = '$postType'"; 
    }

    if (count($fields) === 0) {
        echo json_encode(["success" => false, "message" => "No data provided to update"]);
        exit;
    }

    $sql = "UPDATE `products` SET " . implode(", ", $fields) . " WHERE `id` = $id";

    if (mysqli_query($conn, $sql)) {
        echo json_encode(["success" => true, "message" => "Product updated successfully"]);
    } else {
        echo json_encode(["success" => false, "message" => "Update failed"]);
    }
}

